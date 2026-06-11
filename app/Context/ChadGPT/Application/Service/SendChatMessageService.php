<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Application\Service;

use App\Context\ChadGPT\Application\Data\ChadGptRequestData;
use App\Context\ChadGPT\Domain\Command\CreateChatConversationCommand;
use App\Context\ChadGPT\Infrastructure\Repository\ConversationRepository;
use App\Context\Common\Infrastructure\CommandBus;
use App\Context\User\Domain\Model\User;
use Illuminate\Support\Facades\Log;
use Throwable;

final class SendChatMessageService
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly ChadGptRequestService $chadGptRequestService,
    ) {
    }

    /**
     * @param ChadGptRequestData $data
     * @param User $user
     * @return array{success: bool, response: string, used_words_count: int, error: string|null}
     */
    public function sendMessage(ChadGptRequestData $data, User $user): array
    {
        $response = $this->chadGptRequestService->request($data);

        if (!$response->successful()) {
            Log::error('ChadGPT: API connection failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'response' => '',
                'used_words_count' => 0,
                'error' => 'Не удалось подключиться к ChadGPT API',
            ];
        }

        /** @var array{is_success: ?bool, response: string, used_words_count: int, error_message: ?string} $responseData */
        $responseData = $response->json();

        if (!($responseData['is_success'] ?? false)) {
            Log::error('ChadGPT: API error response', $responseData);

            return [
                'success' => false,
                'response' => '',
                'used_words_count' => 0,
                'error' => $responseData['error_message'] ?? 'Неизвестная ошибка API',
            ];
        }

        $userWordsCount = $responseData['used_words_count'];

        try {
            $command = new CreateChatConversationCommand(
                user: $user,
                model: $data->model,
                userMessage: $data->userMessage,
                response: $responseData['response'],
                userWordsCount: $userWordsCount,
            );
            $this->commandBus->execute($command);
        } catch (Throwable $e) {
            Log::error('ChadGPT: failed to save conversation', [
                'error' => $e->getMessage(),
                'user_id' => $user->getId(),
            ]);
        }

        return [
            'success' => true,
            'response' => $responseData['response'],
            'used_words_count' => $userWordsCount,
            'error' => null,
        ];
    }

    /**
     * @param User $user
     * @param ConversationRepository $conversationRepository
     * @return array{success: bool, error: string|null}
     */
    public function clearHistory(User $user, ConversationRepository $conversationRepository): array
    {
        try {
            $conversationRepository->deleteByUser($user);

            return [
                'success' => true,
                'error' => null,
            ];
        } catch (Throwable $e) {
            Log::error('ChadGPT: не удалось очистить историю', [
                'error' => $e->getMessage(),
                'user_id' => $user->getId(),
            ]);

            return [
                'success' => false,
                'error' => 'Не удалось очистить историю чатов',
            ];
        }
    }
}
