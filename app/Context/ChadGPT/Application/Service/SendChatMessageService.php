<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Application\Service;

use App\Context\ChadGPT\Application\Data\ChadGptRequestData;
use App\Context\ChadGPT\Domain\Command\CreateChatConversationCommand;
use App\Context\ChadGPT\Domain\Model\ChadGptConversation;
use App\Context\ChadGPT\Infrastructure\Repository\ConversationRepository;
use App\Context\Common\Infrastructure\CommandBus;
use App\Context\User\Domain\Model\User;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

final class SendChatMessageService
{
    private const int HISTORY_CONVERSATIONS_LIMIT = 10;

    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly ChadGptRequestService $chadGptRequestService,
        private readonly ConversationRepository $conversationRepository,
    ) {
    }

    /**
     * @param ChadGptRequestData $data
     * @param User $user
     * @return array{success: bool, response: string, used_tokens_count: int, error: string|null}
     */
    public function sendMessage(ChadGptRequestData $data, User $user): array
    {
        $data = $this->withHistory($data, $user);

        $response = $this->chadGptRequestService->request($data);

        if (!$response->successful()) {
            Log::error('ChadGPT: API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'response' => '',
                'used_tokens_count' => 0,
                'error' => $this->extractErrorMessage($response),
            ];
        }

        /** @var array{choices?: array<int, array{message?: array{content?: string}}>, usage?: array{total_tokens?: int}} $responseData */
        $responseData = $response->json();

        $aiResponse = $responseData['choices'][0]['message']['content'] ?? '';
        $usedTokensCount = $responseData['usage']['total_tokens'] ?? 0;

        try {
            $command = new CreateChatConversationCommand(
                user: $user,
                model: $data->model,
                userMessage: $data->userMessage,
                response: $aiResponse,
                userTokensCount: $usedTokensCount,
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
            'response' => $aiResponse,
            'used_tokens_count' => $usedTokensCount,
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

    private function withHistory(ChadGptRequestData $data, User $user): ChadGptRequestData
    {
        if ($data->history !== null) {
            return $data;
        }

        return new ChadGptRequestData(
            model: $data->model,
            userMessage: $data->userMessage,
            temperature: $data->temperature,
            maxTokens: $data->maxTokens,
            history: $this->buildHistory($user),
            images: $data->images,
        );
    }

    /**
     * @return array<int, array{role: string, content: string}>
     */
    private function buildHistory(User $user): array
    {
        return $this->conversationRepository
            ->findBuUser($user)
            ->reverse()
            ->take(self::HISTORY_CONVERSATIONS_LIMIT)
            ->flatMap(static function (ChadGptConversation $conversation): array {
                return [
                    ['role' => 'user', 'content' => $conversation->user_message],
                    ['role' => 'assistant', 'content' => $conversation->ai_response],
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param Response $response
     * @return string
     */
    private function extractErrorMessage(Response $response): string
    {
        /** @var array{error?: array{message?: string}} $responseData */
        $responseData = $response->json();

        if (isset($responseData['error']['message'])) {
            return $responseData['error']['message'];
        }

        return 'Не удалось подключиться к ChadGPT API';
    }
}
