<?php

declare(strict_types=1);

namespace Tests\Feature\ChadGPT;

use App\Context\Common\Infrastructure\CommandBus;
use App\Context\ChadGPT\Application\Service\ChadGptRequestService;
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
            ['user_id' => $this->user->id, 'stat_date' => Carbon::now()->firstOfMonth(), 'tokens_used' => 100]
        );

        ChadGptConversation::factory()->create(['user_id' => $this->user->id]);

        $service = Mockery::mock(ChadGptRequestService::class);
        $service->shouldReceive('getModels')->once()->andReturn([]);
        app()->instance(ChadGptRequestService::class, $service);

        $this->actingAs($this->user);
        $response = $this->get(route('chadgpt.index'));

        $response->assertStatus(200);
        $response->assertViewIs('personal.chadgpt.index');
        $response->assertViewHas('conversations');
        $response->assertViewHas('word_stats');
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
                'message' => 'История чатов успешно очищена'
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
                'error' => 'Не удалось очистить историю чатов'
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
        $service->method('getModelIds')->willReturn(['gpt-5.6-terra']);

        $response = Mockery::mock(Response::class);
        $response->shouldReceive('successful')->andReturn(true);
        $response->shouldReceive('json')->andReturn([
            'choices' => [
                ['message' => ['content' => $responseText]],
            ],
            'usage' => [
                'prompt_tokens' => 30,
                'completion_tokens' => 70,
                'total_tokens' => 100,
            ],
        ]);


        $service->expects($this->once())->method('request')->willReturn($response);

        app()->instance(ChadGptRequestService::class, $service);

        $response = $this->postJson(route('chadgpt.send-message'), [
            'message' => $responseText,
            'model' => 'gpt-5.6-terra'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'response' => $responseText,
                'used_tokens_count' => 100,
            ]);
    }

    public function test_send_message_error_save(): void
    {
        $responseText = 'Тестовый запрос';
        $this->actingAs($this->user);

        $bus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bus->expects($this->once())->method('execute')->willThrowException(new \Exception('Database error'));

        app()->instance(CommandBus::class, $bus);

        $service = $this->getMockBuilder(ChadGptRequestService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $service->method('getModelIds')->willReturn(['gpt-5.6-terra']);

        $responseChad = Mockery::mock(Response::class);
        $responseChad->shouldReceive('successful')->andReturn(true);
        $responseChad->shouldReceive('json')->andReturn([
            'choices' => [
                ['message' => ['content' => $responseText]],
            ],
            'usage' => [
                'total_tokens' => 100,
            ],
        ]);


        $service->expects($this->once())->method('request')->willReturn($responseChad);

        app()->instance(ChadGptRequestService::class, $service);

        Log::shouldReceive('info')->once();
        Log::shouldReceive('error')->once();

        $response = $this->postJson(route('chadgpt.send-message'), [
            'message' => $responseText,
            'model' => 'gpt-5.6-terra'
        ]);

        $response->assertStatus(200);
    }

    public function test_send_message_error_error_api_response(): void
    {
        $responseText = 'Тестовый запрос';
        $this->actingAs($this->user);

        $bus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bus->expects($this->never())->method('execute');

        app()->instance(CommandBus::class, $bus);

        $service = $this->getMockBuilder(ChadGptRequestService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $service->method('getModelIds')->willReturn(['gpt-5.6-terra']);

        $responseChad = Mockery::mock(Response::class);
        $responseChad->shouldReceive('successful')->andReturn(false);
        $responseChad->shouldReceive('status')->andReturn(429);
        $responseChad->shouldReceive('body')->andReturn('{"error":{"message":"insufficient_quota"}}');
        $responseChad->shouldReceive('json')->andReturn([
            'error' => [
                'message' => 'insufficient_quota',
                'type' => 'invalid_request_error',
            ],
        ]);


        $service->expects($this->once())->method('request')->willReturn($responseChad);

        app()->instance(ChadGptRequestService::class, $service);

        Log::shouldReceive('info')->once();
        Log::shouldReceive('error')->once();

        $response = $this->postJson(route('chadgpt.send-message'), [
            'message' => $responseText,
            'model' => 'gpt-5.6-terra'
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'insufficient_quota',
            ]);
    }

    public function test_send_message_validation_failed(): void
    {
        $responseText = str_repeat('a', 1001);
        $this->actingAs($this->user);

        $bus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bus->expects($this->never())->method('execute');

        app()->instance(CommandBus::class, $bus);

        $service = $this->getMockBuilder(ChadGptRequestService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $service->method('getModelIds')->willReturn(['gpt-5.6-terra']);


        $service->expects($this->never())->method('request');

        app()->instance(ChadGptRequestService::class, $service);

        Log::shouldReceive('info')->never();
        Log::shouldReceive('error')->never();

        $response = $this->postJson(route('chadgpt.send-message'), [
            'message' => $responseText,
            'model' => 'gpt-5.6-terra'
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'message' => ['Количество символов в поле message не может превышать 1000.'],
            ]
        ]);
    }

    public function test_send_message_uses_default_model_when_model_not_provided(): void
    {
        $responseText = 'Ответ';
        $this->actingAs($this->user);

        $bus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bus->expects($this->once())->method('execute');

        app()->instance(CommandBus::class, $bus);

        $service = $this->getMockBuilder(ChadGptRequestService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $service->method('getModelIds')->willReturn(['gpt-5.6-terra']);
        $service->method('getDefaultModelId')->willReturn('gpt-5.6-terra');

        $responseChad = Mockery::mock(Response::class);
        $responseChad->shouldReceive('successful')->andReturn(true);
        $responseChad->shouldReceive('json')->andReturn([
            'choices' => [
                ['message' => ['content' => $responseText]],
            ],
            'usage' => [
                'total_tokens' => 10,
            ],
        ]);

        $service->expects($this->once())->method('request')->willReturn($responseChad);

        app()->instance(ChadGptRequestService::class, $service);

        Log::shouldReceive('info')->once();

        $response = $this->postJson(route('chadgpt.send-message'), [
            'message' => 'Привет',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'response' => $responseText,
                'used_tokens_count' => 10,
            ]);
    }

    public function test_send_message_passes_optional_params(): void
    {
        $responseText = 'Ответ';
        $this->actingAs($this->user);

        $bus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bus->expects($this->once())->method('execute');

        app()->instance(CommandBus::class, $bus);

        $service = $this->getMockBuilder(ChadGptRequestService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $service->method('getModelIds')->willReturn(['gpt-5.6-terra']);

        $responseChad = Mockery::mock(Response::class);
        $responseChad->shouldReceive('successful')->andReturn(true);
        $responseChad->shouldReceive('json')->andReturn([
            'choices' => [
                ['message' => ['content' => $responseText]],
            ],
            'usage' => [
                'total_tokens' => 10,
            ],
        ]);

        $service->expects($this->once())->method('request')->willReturn($responseChad);

        app()->instance(ChadGptRequestService::class, $service);

        Log::shouldReceive('info')->once();

        $response = $this->postJson(route('chadgpt.send-message'), [
            'message' => 'Привет',
            'model' => 'gpt-5.6-terra',
            'temperature' => 0.7,
            'max_tokens' => 100,
            'images' => ['https://example.com/img.jpg'],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'response' => $responseText,
            ]);
    }

    public function test_send_message_returns_internal_error_on_exception(): void
    {
        $this->actingAs($this->user);

        $bus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bus->expects($this->never())->method('execute');

        app()->instance(CommandBus::class, $bus);

        $service = $this->getMockBuilder(ChadGptRequestService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $service->method('getModelIds')->willReturn(['gpt-5.6-terra']);
        $service->method('request')->willThrowException(new \RuntimeException('Connection failed'));

        app()->instance(ChadGptRequestService::class, $service);

        Log::shouldReceive('info')->once();
        Log::shouldReceive('error')->once();

        $response = $this->postJson(route('chadgpt.send-message'), [
            'message' => 'Привет',
            'model' => 'gpt-5.6-terra'
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Произошла ошибка при общении с ChadGPT',
            ]);
    }
}
