<?php

declare(strict_types=1);

namespace Tests\Feature\ChadGPT;

use App\Context\ChadGPT\Application\Data\ChadGptModel;
use App\Context\ChadGPT\Application\Service\ChadGptRequestService;
use App\Context\ChadGPT\Domain\Model\ChadGptConversation;
use App\Context\ChadGPT\Domain\Model\ChadGptConversationWordStat;
use App\Context\ChadGPT\Infrastructure\Repository\ConversationRepository;
use App\Context\User\Domain\Model\User;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Log;
use Mockery;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Tests\TestCase;

final class ChatsControllerApiTest extends TestCase
{
    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var User $user */
        $user = User::factory()->create();
        $this->user = $user;
        $this->token = $user->createToken('test-token')->plainTextToken;
    }

    /**
     * @return array<string, string>
     */
    private function authHeaders(): array
    {
        return ['Authorization' => 'Bearer ' . $this->token];
    }

    /**
     * @return MockObject&ChadGptRequestService
     */
    private function mockRequestService(): ChadGptRequestService
    {
        $service = $this->getMockBuilder(ChadGptRequestService::class)
            ->disableOriginalConstructor()
            ->getMock();
        app()->instance(ChadGptRequestService::class, $service);

        return $service;
    }

    public function test_index_requires_authentication(): void
    {
        $this->getJson(route('chats.index'))->assertStatus(401);
    }

    public function test_send_message_requires_authentication(): void
    {
        $this->postJson(route('api.chats.send'), ['message' => 'hello'])->assertStatus(401);
    }

    public function test_clear_history_requires_authentication(): void
    {
        $this->deleteJson(route('api.chats.clear'))->assertStatus(401);
    }

    public function test_index_returns_models_conversations_and_stats(): void
    {
        ChadGptConversationWordStat::create([
            'user_id' => $this->user->id,
            'stat_date' => Carbon::now()->firstOfMonth(),
            'tokens_used' => 150,
        ]);

        ChadGptConversation::factory()->count(2)->create(['user_id' => $this->user->id]);

        $service = $this->mockRequestService();
        $service->method('getModels')->willReturn([
            new ChadGptModel(id: 'gpt-5.6-terra', label: 'GPT 5.6 Terra', isDefault: true),
        ]);

        $this->getJson(route('chats.index'), $this->authHeaders())
            ->assertOk()
            ->assertJsonStructure([
                'models',
                'conversations',
                'word_stats',
                'word_stats_sum',
            ])
            ->assertJsonCount(1, 'models')
            ->assertJsonCount(2, 'conversations')
            ->assertJsonCount(1, 'word_stats')
            ->assertJson([
                'word_stats_sum' => 150,
                'models' => [
                    ['id' => 'gpt-5.6-terra', 'label' => 'GPT 5.6 Terra', 'isDefault' => true],
                ],
            ]);
    }

    public function test_send_message_success(): void
    {
        $responseText = 'Ответ от ChadGPT';

        $service = $this->mockRequestService();
        $service->method('getModelIds')->willReturn(['gpt-5.6-terra']);

        $chadResponse = Mockery::mock(Response::class);
        $chadResponse->shouldReceive('successful')->andReturn(true);
        $chadResponse->shouldReceive('json')->andReturn([
            'choices' => [['message' => ['content' => $responseText]]],
            'usage' => ['total_tokens' => 42],
        ]);

        $service->expects($this->once())->method('request')->willReturn($chadResponse);

        Log::shouldReceive('info')->once();

        $this->postJson(route('api.chats.send'), ['message' => 'Привет', 'model' => 'gpt-5.6-terra'], $this->authHeaders())
            ->assertOk()
            ->assertJson([
                'success' => true,
                'response' => $responseText,
                'used_tokens_count' => 42,
            ]);
    }

    public function test_send_message_uses_default_model_when_not_provided(): void
    {
        $service = $this->mockRequestService();
        $service->method('getModelIds')->willReturn(['gpt-5.6-terra']);
        $service->method('getDefaultModelId')->willReturn('gpt-5.6-terra');

        $chadResponse = Mockery::mock(Response::class);
        $chadResponse->shouldReceive('successful')->andReturn(true);
        $chadResponse->shouldReceive('json')->andReturn([
            'choices' => [['message' => ['content' => 'ok']]],
            'usage' => ['total_tokens' => 5],
        ]);

        $service->expects($this->once())->method('request')->willReturn($chadResponse);

        Log::shouldReceive('info')->once();

        $this->postJson(route('api.chats.send'), ['message' => 'Привет'], $this->authHeaders())
            ->assertOk()
            ->assertJson(['success' => true, 'response' => 'ok']);
    }

    public function test_send_message_returns_api_error(): void
    {
        $service = $this->mockRequestService();
        $service->method('getModelIds')->willReturn(['gpt-5.6-terra']);

        $chadResponse = Mockery::mock(Response::class);
        $chadResponse->shouldReceive('successful')->andReturn(false);
        $chadResponse->shouldReceive('status')->andReturn(429);
        $chadResponse->shouldReceive('body')->andReturn('{"error":{"message":"insufficient_quota"}}');
        $chadResponse->shouldReceive('json')->andReturn([
            'error' => ['message' => 'insufficient_quota'],
        ]);

        $service->expects($this->once())->method('request')->willReturn($chadResponse);

        Log::shouldReceive('info')->once();
        Log::shouldReceive('error')->once();

        $this->postJson(route('api.chats.send'), ['message' => 'Привет'], $this->authHeaders())
            ->assertStatus(400)
            ->assertJson(['error' => 'insufficient_quota']);
    }

    public function test_send_message_returns_internal_error_on_exception(): void
    {
        $service = $this->mockRequestService();
        $service->method('getModelIds')->willReturn(['gpt-5.6-terra']);
        $service->method('request')->willThrowException(new RuntimeException('Connection failed'));

        Log::shouldReceive('info')->once();
        Log::shouldReceive('error')->once();

        $this->postJson(route('api.chats.send'), ['message' => 'Привет'], $this->authHeaders())
            ->assertStatus(500)
            ->assertJson(['error' => 'Произошла ошибка при общении с ChadGPT']);
    }

    public function test_send_message_validation_failed(): void
    {
        $this->mockRequestService()->method('getModelIds')->willReturn(['gpt-5.6-terra']);

        Log::shouldReceive('info')->never();
        Log::shouldReceive('error')->never();

        $this->postJson(route('api.chats.send'), ['message' => str_repeat('a', 1001)], $this->authHeaders())
            ->assertStatus(422)
            ->assertJsonStructure(['errors' => ['message']]);
    }

    public function test_send_message_validates_model_against_available_models(): void
    {
        $this->mockRequestService()->method('getModelIds')->willReturn(['gpt-5.6-terra']);

        $this->postJson(
            route('api.chats.send'),
            ['message' => 'Привет', 'model' => 'not-available-model'],
            $this->authHeaders(),
        )
            ->assertStatus(422)
            ->assertJsonStructure(['errors' => ['model']]);
    }

    public function test_clear_history_success(): void
    {
        ChadGptConversation::factory()->count(3)->create(['user_id' => $this->user->id]);

        $this->deleteJson(route('api.chats.clear'), [], $this->authHeaders())
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'История чатов успешно очищена',
            ]);

        $this->assertSame(0, ChadGptConversation::where('user_id', $this->user->id)->count());
    }

    public function test_clear_history_returns_error_on_database_exception(): void
    {
        $this->mock(ConversationRepository::class, static function ($mock): void {
            $mock->shouldReceive('deleteByUser')
                ->andThrow(new RuntimeException('Database error'));
        });

        $this->deleteJson(route('api.chats.clear'), [], $this->authHeaders())
            ->assertStatus(500)
            ->assertJson([
                'success' => false,
                'error' => 'Не удалось очистить историю чатов',
            ]);
    }
}
