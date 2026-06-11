<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Infrastructure\Http\Controllers\Api;

use App\Context\ChadGPT\Application\Data\ChadGptRequestData;
use App\Context\ChadGPT\Application\Service\SendChatMessageService;
use App\Context\ChadGPT\Infrastructure\Repository\ConversationRepository;
use App\Context\ChadGPT\Infrastructure\Repository\StatWordsUsedRepository;
use App\Context\ChadGPT\Infrastructure\Request\SendMessageRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
    public function index(Request $request, ConversationRepository $conversationRepository, StatWordsUsedRepository $statWordsUsedRepository): JsonResponse
    {
        $user = $request->user();
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
        SendMessageRequest $request,
        SendChatMessageService $sendChatMessageService,
    ): JsonResponse {
        Log::info('ChadGPT: sending message', ['request' => $request->all()]);

        try {
            $chadGptRequestData = ChadGptRequestData::from([
                'model' => $request->input('model', 'gpt-4o-mini'),
                'userMessage' => $request->string('message')->value(),
            ]);

            $result = $sendChatMessageService->sendMessage($chadGptRequestData, $request->user());

            if (!$result['success']) {
                return response()->json(
                    ['error' => $result['error']],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return response()->json([
                'success' => true,
                'response' => $result['response'],
                'used_words_count' => $result['used_words_count'],
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
    public function clearHistory(
        Request $request,
        ConversationRepository $conversationRepository,
        SendChatMessageService $sendChatMessageService,
    ): JsonResponse {
        $result = $sendChatMessageService->clearHistory($request->user(), $conversationRepository);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'История чатов успешно очищена',
        ]);
    }
}
