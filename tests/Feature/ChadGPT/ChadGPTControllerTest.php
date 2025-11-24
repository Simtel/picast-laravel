<?php

declare(strict_types=1);

namespace Tests\Feature\ChadGPT;

use App\Common\CommandBus;
use App\Context\ChadGPT\Application\Service\ChadGptRequestService;
use App\Context\ChadGPT\Domain\ChatModels;
use App\Context\ChadGPT\Domain\Model\ChadGptConversation;
use App\Context\ChadGPT\Infrastructure\Repository\ConversationRepository;
use App\Context\User\Domain\Model\User;
use Illuminate\Http\Client\Response;
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


    public function testSendMessageSuccessfully(): void
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
}
