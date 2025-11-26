<?php

declare(strict_types=1);

namespace Tests\Feature\ChadGPT;

use App\Common\CommandBus;
use App\Context\ChadGPT\Application\Service\ChadGptRequestService;
use App\Context\ChadGPT\Domain\ChatModels;
use App\Context\ChadGPT\Domain\Model\ChadGptConversation;
use App\Context\ChadGPT\Domain\Model\ChadGptConversationWordStat;
use App\Context\ChadGPT\Infrastructure\Repository\ConversationRepository;
use App\Context\User\Domain\Model\User;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Log;
use Mockery;
use Tests\TestCase;

class ChadGPTControllerTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var User $user */
        $user = User::factory()->create();
        $this->user = $user;
    }

    /**
     * Test that the index method returns a view
     */
    public function test_index_returns_view(): void
    {
        ChadGptConversationWordStat::create(
            ['user_id' => $this->user->id, 'stat_date' => Carbon::now()->firstOfMonth(), 'words_used' => 100]
        );

        ChadGptConversation::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user);
        $response = $this->get(route('chadgpt.index'));

        $response->assertStatus(200);
        $response->assertViewIs('personal.chadgpt.index');
        $response->assertViewHas('conversations');
    }

    public function test_clear_history_removes_all_user_conversations(): void
    {
        $this->actingAs($this->user);


        ChadGptConversation::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);

        $otherUser = User::factory()->create();
        ChadGptConversation::factory()->create([
            'user_id' => $otherUser->id
        ]);


        $response = $this->deleteJson(route('chadgpt.clear-history'));


        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Chat history cleared successfully'
            ]);

        $this->assertEquals(0, ChadGptConversation::where('user_id', $this->user->id)->count());
        $this->assertEquals(1, ChadGptConversation::where('user_id', $otherUser->id)->count());
    }

    public function test_clear_history_returns_error_when_not_authenticated(): void
    {
        $response = $this->deleteJson(route('chadgpt.clear-history'));


        $response->assertStatus(401); // Unauthorized
    }

    public function test_clear_history_handles_database_exception(): void
    {
        $this->actingAs($this->user);


        ChadGptConversation::factory()->count(2)->create([
            'user_id' => $this->user->id
        ]);


        $this->mock(ConversationRepository::class, static function ($mock) {
            $mock->shouldReceive('deleteByUser')
                ->andThrow(new \Exception('Database error'));
        });


        $response = $this->deleteJson(route('chadgpt.clear-history'));


        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
                'error' => 'Failed to clear chat history'
            ]);

        $this->assertEquals(2, ChadGptConversation::where('user_id', $this->user->id)->count());
    }


    public function test_send_message_successfully(): void
    {
        $responseText = 'Тестовый запрос';
        $usedWordsCount = 100;
        $this->actingAs($this->user);

        $bus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bus->expects($this->once())->method('execute');

        app()->instance(CommandBus::class, $bus);

        $service = $this->getMockBuilder(ChadGptRequestService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = Mockery::mock(Response::class);
        $response->shouldReceive('successful')->andReturn(true);
        $response->shouldReceive('json')->andReturn([
            'is_success' => true,
            'response' => $responseText,
            'used_words_count' => $usedWordsCount,
            'used_tokens_count' => 20,
            'error_code' => null,
            'error_message' => null,
        ]);


        $service->expects($this->once())->method('request')->willReturn($response);

        app()->instance(ChadGptRequestService::class, $service);

        $this->postJson(route('chadgpt.send-message'), [
            'message' => $responseText,
            'model' => ChatModels::GPT_4O_MINI
        ]);
    }

    public function test_send_message_error_save(): void
    {
        $responseText = 'Тестовый запрос';
        $usedWordsCount = 100;
        $this->actingAs($this->user);

        $bus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bus->expects($this->once())->method('execute')->willThrowException(new \Exception('Database error'));

        app()->instance(CommandBus::class, $bus);

        $service = $this->getMockBuilder(ChadGptRequestService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseChad = Mockery::mock(Response::class);
        $responseChad->shouldReceive('successful')->andReturn(true);
        $responseChad->shouldReceive('json')->andReturn([
            'is_success' => true,
            'response' => $responseText,
            'used_words_count' => $usedWordsCount,
            'used_tokens_count' => 20,
            'error_code' => null,
            'error_message' => null,
        ]);


        $service->expects($this->once())->method('request')->willReturn($responseChad);

        app()->instance(ChadGptRequestService::class, $service);

        Log::shouldReceive('info')->once();
        Log::shouldReceive('error')->once();

        $response = $this->postJson(route('chadgpt.send-message'), [
            'message' => $responseText,
            'model' => ChatModels::GPT_4O_MINI
        ]);

        $response->assertStatus(200);
    }

    public function test_send_message_error_error_api_response(): void
    {
        $responseText = 'Тестовый запрос';
        $usedWordsCount = 100;
        $this->actingAs($this->user);

        $bus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bus->expects($this->never())->method('execute');

        app()->instance(CommandBus::class, $bus);

        $service = $this->getMockBuilder(ChadGptRequestService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseChad = Mockery::mock(Response::class);
        $responseChad->shouldReceive('successful')->andReturn(false);
        $responseChad->shouldReceive('json')->andReturn([
            'is_success' => false,
            'response' => $responseText,
            'used_words_count' => $usedWordsCount,
            'used_tokens_count' => 20,
            'error_code' => null,
            'error_message' => null,
        ]);


        $service->expects($this->once())->method('request')->willReturn($responseChad);

        app()->instance(ChadGptRequestService::class, $service);

        Log::shouldReceive('info')->once();
        Log::shouldReceive('error')->once();

        $response = $this->postJson(route('chadgpt.send-message'), [
            'message' => $responseText,
            'model' => ChatModels::GPT_4O_MINI
        ]);

        $response->assertStatus(500);
    }

    public function test_send_message_validation_failed(): void
    {
        $responseText = str_repeat('a', 1001);
        $usedWordsCount = 100;
        $this->actingAs($this->user);

        $bus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bus->expects($this->never())->method('execute');

        app()->instance(CommandBus::class, $bus);

        $service = $this->getMockBuilder(ChadGptRequestService::class)
            ->disableOriginalConstructor()
            ->getMock();


        $service->expects($this->never())->method('request');

        app()->instance(ChadGptRequestService::class, $service);

        Log::shouldReceive('info')->never();
        Log::shouldReceive('error')->never();

        $response = $this->postJson(route('chadgpt.send-message'), [
            'message' => $responseText,
            'model' => ChatModels::GPT_4O_MINI
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'message' => ['Количество символов в поле message не может превышать 1000.'],
            ]
        ]);
    }
}
