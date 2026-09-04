<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Application\Service;

use App\Context\ChadGPT\Application\Data\ChadGptRequestData;
use App\Context\ChadGPT\Infrastructure\Repository\ConversationRepository;
use App\Context\ChadGPT\Infrastructure\Repository\StatWordsUsedRepository;
use App\Context\User\Domain\Model\User;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ChatService
{
    public function __construct(
        private readonly ChadGptRequestService $chadGptRequestService,
        private readonly SendChatMessageService $sendChatMessageService,
        private readonly ConversationRepository $conversationRepository,
        private readonly StatWordsUsedRepository $statWordsUsedRepository,
    ) {
    }

    /**
     * Общие данные для страницы чата и API.
     *
     * @return array{models: array<int, \App\Context\ChadGPT\Application\Data\ChadGptModel>, conversations: \Illuminate\Support\Collection<int, \App\Context\ChadGPT\Domain\Model\ChadGptConversation>, word_stats: \Illuminate\Support\Collection<int, \App\Context\ChadGPT\Domain\Model\ChadGptConversationWordStat>, word_stats_sum: int}
     */
    public function conversationData(User $user): array
    {
        $wordStats = $this->statWordsUsedRepository->findByUser($user);

        return [
            'models' => $this->chadGptRequestService->getModels(),
            'conversations' => $this->conversationRepository->findBuUser($user),
            'word_stats' => $wordStats,
            'word_stats_sum' => $wordStats->sum(static fn ($stat) => $stat->getTokensUsed()),
        ];
    }

    /**
     * @param User $user
     * @param ChadGptRequestData $data
     * @return array{status: int, body: array<string, mixed>}
     */
    public function sendMessage(User $user, ChadGptRequestData $data): array
    {
        Log::info('ChadGPT: sending message', ['message' => $data->userMessage]);

        try {
            $result = $this->sendChatMessageService->sendMessage($data, $user);

            if (!$result['success']) {
                return [
                    'status' => Response::HTTP_BAD_REQUEST,
                    'body' => ['error' => $result['error']],
                ];
            }

            return [
                'status' => Response::HTTP_OK,
                'body' => [
                    'success' => true,
                    'response' => $result['response'],
                    'used_tokens_count' => $result['used_tokens_count'],
                ],
            ];
        } catch (Throwable $e) {
            Log::error('ChadGPT: request exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'body' => ['error' => 'Произошла ошибка при общении с ChadGPT'],
            ];
        }
    }

    /**
     * @param User $user
     * @return array{status: int, body: array<string, mixed>}
     */
    public function clearHistory(User $user): array
    {
        $result = $this->sendChatMessageService->clearHistory($user);

        if (!$result['success']) {
            return [
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'body' => [
                    'success' => false,
                    'error' => $result['error'],
                ],
            ];
        }

        return [
            'status' => Response::HTTP_OK,
            'body' => [
                'success' => true,
                'message' => 'История чатов успешно очищена',
            ],
        ];
    }
}
