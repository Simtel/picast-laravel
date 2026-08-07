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
}
