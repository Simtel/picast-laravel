<?php

declare(strict_types=1);

namespace Tests\Unit\Context\ChadGPT;

use App\Context\ChadGPT\Application\Data\ChadGptRequestData;
use App\Context\ChadGPT\Application\Service\ChadGptRequestService;
use App\Context\ChadGPT\Application\Service\SendChatMessageService;
use App\Context\ChadGPT\Domain\Command\CreateChatConversationCommand;
use App\Context\ChadGPT\Domain\Model\ChadGptConversation;
use App\Context\ChadGPT\Infrastructure\Repository\ConversationRepository;
use App\Context\Common\Infrastructure\CommandBus;
use App\Context\User\Domain\Model\User;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Log;
use Mockery;
use Tests\TestCase;

class SendChatMessageServiceTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var User $user */
        $user = User::factory()->create();
        $this->user = $user;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_send_message_builds_history_from_previous_conversations(): void
    {
        ChadGptConversation::factory()->create([
            'user_id' => $this->user->id,
            'user_message' => 'Вопрос 1',
            'ai_response' => 'Ответ 1',
        ]);
        ChadGptConversation::factory()->create([
            'user_id' => $this->user->id,
            'user_message' => 'Вопрос 2',
            'ai_response' => 'Ответ 2',
        ]);

        $historyCaptured = null;

        $requestService = Mockery::mock(ChadGptRequestService::class);
        $requestService->shouldReceive('request')
            ->once()
            ->withArgs(static function (ChadGptRequestData $data) use (&$historyCaptured) {
                $historyCaptured = $data->history;

                return $data->history !== null;
            })
            ->andReturnUsing(static function () {
                $response = Mockery::mock(Response::class);
                $response->shouldReceive('successful')->andReturn(true);
                $response->shouldReceive('json')->andReturn([
                    'choices' => [
                        ['message' => ['content' => 'Ответ 3']],
                    ],
                    'usage' => [
                        'prompt_tokens' => 10,
                        'completion_tokens' => 15,
                        'total_tokens' => 25,
                    ],
                ]);

                return $response;
            });

        $commandBus = Mockery::mock(CommandBus::class);
        $commandBus->shouldReceive('execute')
            ->once()
            ->with(Mockery::type(CreateChatConversationCommand::class));

        $service = new SendChatMessageService(
            $commandBus,
            $requestService,
            new ConversationRepository(),
        );

        $result = $service->sendMessage(
            ChadGptRequestData::from([
                'model' => 'gpt-5.6-terra',
                'userMessage' => 'Вопрос 3',
            ]),
            $this->user,
        );

        $this->assertTrue($result['success']);

        $this->assertNotNull($historyCaptured);
        $this->assertSame([
            ['role' => 'user', 'content' => 'Вопрос 1'],
            ['role' => 'assistant', 'content' => 'Ответ 1'],
            ['role' => 'user', 'content' => 'Вопрос 2'],
            ['role' => 'assistant', 'content' => 'Ответ 2'],
        ], $historyCaptured);
    }

    public function test_send_message_keeps_explicit_history(): void
    {
        $explicitHistory = [
            ['role' => 'system', 'content' => 'Ты - полезный ассистент'],
        ];

        $requestService = Mockery::mock(ChadGptRequestService::class);
        $requestService->shouldReceive('request')
            ->once()
            ->withArgs(static function (ChadGptRequestData $data) use ($explicitHistory) {
                return $data->history === $explicitHistory;
            })
            ->andReturnUsing(static function () {
                $response = Mockery::mock(Response::class);
                $response->shouldReceive('successful')->andReturn(true);
                $response->shouldReceive('json')->andReturn([
                    'choices' => [
                        ['message' => ['content' => 'Ответ']],
                    ],
                    'usage' => [
                        'total_tokens' => 30,
                    ],
                ]);

                return $response;
            });

        $commandBus = Mockery::mock(CommandBus::class);
        $commandBus->shouldReceive('execute')->once();

        $service = new SendChatMessageService(
            $commandBus,
            $requestService,
            new ConversationRepository(),
        );

        $result = $service->sendMessage(
            ChadGptRequestData::from([
                'model' => 'gpt-5.6-terra',
                'userMessage' => 'Вопрос',
                'history' => $explicitHistory,
            ]),
            $this->user,
        );

        $this->assertTrue($result['success']);
    }

    public function test_send_message_returns_api_error_message_on_failure(): void
    {
        $requestService = Mockery::mock(ChadGptRequestService::class);
        $requestService->shouldReceive('request')
            ->once()
            ->andReturnUsing(static function () {
                $response = Mockery::mock(Response::class);
                $response->shouldReceive('successful')->andReturn(false);
                $response->shouldReceive('status')->andReturn(429);
                $response->shouldReceive('body')->andReturn('{"error":{"message":"insufficient_quota"}}');
                $response->shouldReceive('json')->andReturn([
                    'error' => [
                        'message' => 'insufficient_quota',
                    ],
                ]);

                return $response;
            });

        $commandBus = Mockery::mock(CommandBus::class);
        $commandBus->shouldReceive('execute')->never();

        $service = new SendChatMessageService(
            $commandBus,
            $requestService,
            new ConversationRepository(),
        );

        $result = $service->sendMessage(
            ChadGptRequestData::from([
                'model' => 'gpt-5.6-terra',
                'userMessage' => 'Вопрос',
            ]),
            $this->user,
        );

        $this->assertFalse($result['success']);
        $this->assertSame('insufficient_quota', $result['error']);
        $this->assertSame(0, $result['used_tokens_count']);
    }

    public function test_send_message_uses_fallback_error_message_when_api_returns_no_details(): void
    {
        $requestService = Mockery::mock(ChadGptRequestService::class);
        $requestService->shouldReceive('request')
            ->once()
            ->andReturnUsing(static function () {
                $response = Mockery::mock(Response::class);
                $response->shouldReceive('successful')->andReturn(false);
                $response->shouldReceive('status')->andReturn(500);
                $response->shouldReceive('body')->andReturn('Server error');
                $response->shouldReceive('json')->andReturn([]);

                return $response;
            });

        $commandBus = Mockery::mock(CommandBus::class);
        $commandBus->shouldReceive('execute')->never();

        $service = new SendChatMessageService(
            $commandBus,
            $requestService,
            new ConversationRepository(),
        );

        $result = $service->sendMessage(
            ChadGptRequestData::from([
                'model' => 'gpt-5.6-terra',
                'userMessage' => 'Вопрос',
            ]),
            $this->user,
        );

        $this->assertFalse($result['success']);
        $this->assertSame('Не удалось подключиться к ChadGPT API', $result['error']);
    }

    public function test_send_message_handles_empty_response_content(): void
    {
        $requestService = Mockery::mock(ChadGptRequestService::class);
        $requestService->shouldReceive('request')
            ->once()
            ->andReturnUsing(static function () {
                $response = Mockery::mock(Response::class);
                $response->shouldReceive('successful')->andReturn(true);
                $response->shouldReceive('json')->andReturn([]);

                return $response;
            });

        $commandBus = Mockery::mock(CommandBus::class);
        $commandBus->shouldReceive('execute')
            ->once()
            ->withArgs(static function (CreateChatConversationCommand $command): bool {
                return $command->getResponse() === '' && $command->getUserTokensCount() === 0;
            });

        $service = new SendChatMessageService(
            $commandBus,
            $requestService,
            new ConversationRepository(),
        );

        $result = $service->sendMessage(
            ChadGptRequestData::from([
                'model' => 'gpt-5.6-terra',
                'userMessage' => 'Вопрос',
            ]),
            $this->user,
        );

        $this->assertTrue($result['success']);
        $this->assertSame('', $result['response']);
        $this->assertSame(0, $result['used_tokens_count']);
    }

    public function test_send_message_returns_success_even_if_saving_conversation_fails(): void
    {
        $requestService = Mockery::mock(ChadGptRequestService::class);
        $requestService->shouldReceive('request')
            ->once()
            ->andReturnUsing(static function () {
                $response = Mockery::mock(Response::class);
                $response->shouldReceive('successful')->andReturn(true);
                $response->shouldReceive('json')->andReturn([
                    'choices' => [['message' => ['content' => 'Ответ']]],
                    'usage' => ['total_tokens' => 10],
                ]);

                return $response;
            });

        $commandBus = Mockery::mock(CommandBus::class);
        $commandBus->shouldReceive('execute')
            ->once()
            ->andThrow(new \Exception('Database error'));

        Log::shouldReceive('error')->once();

        $service = new SendChatMessageService(
            $commandBus,
            $requestService,
            new ConversationRepository(),
        );

        $result = $service->sendMessage(
            ChadGptRequestData::from([
                'model' => 'gpt-5.6-terra',
                'userMessage' => 'Вопрос',
            ]),
            $this->user,
        );

        $this->assertTrue($result['success']);
        $this->assertSame('Ответ', $result['response']);
        $this->assertSame(10, $result['used_tokens_count']);
    }

    public function test_send_message_builds_history_with_limit_of_ten_conversations(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            ChadGptConversation::factory()->create([
                'user_id' => $this->user->id,
                'user_message' => 'Вопрос ' . $i,
                'ai_response' => 'Ответ ' . $i,
                'created_at' => Carbon::now()->addMinutes($i),
            ]);
        }

        $historyCaptured = null;

        $requestService = Mockery::mock(ChadGptRequestService::class);
        $requestService->shouldReceive('request')
            ->once()
            ->withArgs(static function (ChadGptRequestData $data) use (&$historyCaptured) {
                $historyCaptured = $data->history;

                return true;
            })
            ->andReturnUsing(static function () {
                $response = Mockery::mock(Response::class);
                $response->shouldReceive('successful')->andReturn(true);
                $response->shouldReceive('json')->andReturn([
                    'choices' => [['message' => ['content' => 'ok']]],
                    'usage' => ['total_tokens' => 1],
                ]);

                return $response;
            });

        $commandBus = Mockery::mock(CommandBus::class);
        $commandBus->shouldReceive('execute')->once();

        $service = new SendChatMessageService(
            $commandBus,
            $requestService,
            new ConversationRepository(),
        );

        $service->sendMessage(
            ChadGptRequestData::from([
                'model' => 'gpt-5.6-terra',
                'userMessage' => 'Вопрос 16',
            ]),
            $this->user,
        );

        $this->assertNotNull($historyCaptured);
        $this->assertCount(20, $historyCaptured);
        $this->assertSame('Вопрос 1', $historyCaptured[0]['content']);
        $this->assertSame('Вопрос 10', $historyCaptured[18]['content']);
    }

    public function test_clear_history_success(): void
    {
        ChadGptConversation::factory()->count(3)->create(['user_id' => $this->user->id]);

        $commandBus = Mockery::mock(CommandBus::class);
        $requestService = Mockery::mock(ChadGptRequestService::class);

        $service = new SendChatMessageService(
            $commandBus,
            $requestService,
            new ConversationRepository(),
        );

        $result = $service->clearHistory($this->user);

        $this->assertTrue($result['success']);
        $this->assertNull($result['error']);
        $this->assertSame(0, ChadGptConversation::where('user_id', $this->user->id)->count());
    }

    public function test_clear_history_returns_error_on_exception(): void
    {
        $repository = Mockery::mock(ConversationRepository::class);
        $repository->shouldReceive('deleteByUser')
            ->once()
            ->andThrow(new \Exception('Database error'));

        Log::shouldReceive('error')->once();

        $service = new SendChatMessageService(
            Mockery::mock(CommandBus::class),
            Mockery::mock(ChadGptRequestService::class),
            $repository,
        );

        $result = $service->clearHistory($this->user);

        $this->assertFalse($result['success']);
        $this->assertSame('Не удалось очистить историю чатов', $result['error']);
    }
}
