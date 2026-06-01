<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Infrastructure\Http\Controllers\Api;

use App\Context\ChadGPT\Application\Data\ChadGptRequestData;
use App\Context\ChadGPT\Application\Service\ChadGptRequestService;
use App\Context\ChadGPT\Domain\ChatModels;
use App\Context\ChadGPT\Domain\Command\CreateChatConversationCommand;
use App\Context\ChadGPT\Infrastructure\Repository\ConversationRepository;
use App\Context\ChadGPT\Infrastructure\Repository\StatWordsUsedRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;
use Throwable;

#[OA\Tag(
    name: "ChadGPT",
    description: "API для взаимодействия с чат-ботом ChadGPT"
)]
#[OA\Schema(
    schema: "ChatConversation",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "user_id", type: "integer", example: 1),
        new OA\Property(property: "model", type: "string", example: "gpt-4o-mini"),
        new OA\Property(property: "user_message", type: "string", example: "Привет!"),
        new OA\Property(property: "ai_response", type: "string", example: "Привет! Чем могу помочь?"),
        new OA\Property(property: "used_words_count", type: "integer", example: 5),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-10-11T10:00:00Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-10-11T10:00:00Z"),
    ],
    type: "object"
)]
final class ChatsController extends Controller
{
    #[OA\Get(
        path: '/api/v1/chats',
        summary: 'Получить историю чатов пользователя',
        security: [['sanctum' => []]],
        tags: ['ChadGPT'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список чатов пользователя',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/ChatConversation')
                )
            )
        ]
    )]
    public function index(ConversationRepository $conversationRepository, StatWordsUsedRepository $statWordsUsedRepository): JsonResponse
    {
        $user = Auth::user();
        $wordStats = $statWordsUsedRepository->findByUser($user);

        return response()->json([
            'conversations' => $conversationRepository->findBuUser($user),
            'word_stats' => $wordStats,
            'word_stats_sum' => $wordStats->sum(static fn ($stat) => $stat->getWordsUsed()),
        ]);
    }

    #[OA\Post(
        path: '/api/v1/chats',
        summary: 'Отправить сообщение в ChadGPT',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['message'],
                properties: [
                    new OA\Property(property: 'message', description: 'Текст сообщения', type: 'string', example: 'Привет!'),
                    new OA\Property(property: 'model', description: 'Модель ИИ', type: 'string', example: 'gpt-4o-mini'),
                ]
            )
        ),
        tags: ['ChadGPT'],
        responses: [
            new OA\Response(response: 200, description: 'Ответ от ChadGPT'),
            new OA\Response(response: 422, description: 'Ошибка валидации'),
            new OA\Response(response: 500, description: 'Ошибка сервера'),
        ]
    )]
    public function sendMessage(
        \Illuminate\Http\Request $request,
        \App\Context\Common\Infrastructure\CommandBus $commandBus,
        ChadGptRequestService $chadGptRequestService
    ): JsonResponse {
        Log::info('ChadGPT: sending message', ['request' => $request->all()]);

        try {
            $validated = $request->validate([
                'message' => 'required|string',
                'model' => 'sometimes|string|in:' . implode(',', ChatModels::values()),
            ]);

            $chadGptRequestData = ChadGptRequestData::from([
                'model' => $request->input('model', 'gpt-4o-mini'),
                'userMessage' => $request->string('message')->value(),
            ]);

            $response = $chadGptRequestService->request($chadGptRequestData);

            if (!$response->successful()) {
                Log::error('ChadGPT: API connection failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json(
                    ['error' => 'Не удалось подключиться к ChadGPT API'],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            /** @var array{is_success: ?bool, response: string, used_words_count: int, error_message: ?string} $data */
            $data = $response->json();

            if (!($data['is_success'] ?? false)) {
                Log::error('ChadGPT: API error response', $data);

                return response()->json(
                    ['error' => $data['error_message'] ?? 'Неизвестная ошибка API'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $userWordsCount = $data['used_words_count'];

            try {
                $command = new CreateChatConversationCommand(
                    user: Auth::user(),
                    model: $chadGptRequestData->model,
                    userMessage: $chadGptRequestData->userMessage,
                    response: $data['response'],
                    userWordsCount: $userWordsCount,
                );
                $commandBus->execute($command);
            } catch (Throwable $e) {
                Log::error('ChadGPT: failed to save conversation', [
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id(),
                ]);
            }

            return response()->json([
                'success' => true,
                'response' => $data['response'],
                'used_words_count' => $userWordsCount,
            ]);
        } catch (Throwable $e) {
            Log::error('ChadGPT: request exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(
                ['error' => 'Произошла ошибка при общении с ChadGPT'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[OA\Delete(
        path: '/api/v1/chats',
        summary: 'Очистить историю чатов пользователя',
        security: [['sanctum' => []]],
        tags: ['ChadGPT'],
        responses: [
            new OA\Response(response: 200, description: 'История успешно очищена'),
            new OA\Response(response: 500, description: 'Ошибка сервера'),
        ]
    )]
    public function clearHistory(ConversationRepository $conversationRepository): JsonResponse
    {
        try {
            $conversationRepository->deleteByUser(Auth::user());

            return response()->json([
                'success' => true,
                'message' => 'История чатов успешно очищена',
            ]);
        } catch (Throwable $e) {
            Log::error('ChadGPT: не удалось очистить историю', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return response()->json(
                ['success' => false, 'error' => 'Не удалось очистить историю чатов'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
