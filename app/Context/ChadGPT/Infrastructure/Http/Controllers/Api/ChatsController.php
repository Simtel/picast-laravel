<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Infrastructure\Http\Controllers\Api;

use App\Context\ChadGPT\Application\Service\ChatService;
use App\Context\ChadGPT\Infrastructure\Request\SendMessageRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "ChadGPT",
    description: "API для взаимодействия с чат-ботом ChadGPT"
)]
#[OA\Schema(
    schema: "ChatConversation",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "user_id", type: "integer", example: 1),
        new OA\Property(property: "model", type: "string", example: "gpt-5.6-terra"),
        new OA\Property(property: "user_message", type: "string", example: "Привет!"),
        new OA\Property(property: "ai_response", type: "string", example: "Привет! Чем могу помочь?"),
        new OA\Property(property: "used_tokens_count", type: "integer", example: 120),
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
    public function index(Request $request, ChatService $chatService): JsonResponse
    {
        return response()->json($chatService->conversationData($request->user()));
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
                    new OA\Property(property: 'model', description: 'Модель ИИ', type: 'string', example: 'gpt-5.6-terra'),
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
        ChatService $chatService,
    ): JsonResponse {
        $result = $chatService->sendMessage($request->user(), $request->toData());

        return response()->json($result['body'], $result['status']);
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
        ChatService $chatService,
    ): JsonResponse {
        $result = $chatService->clearHistory($request->user());

        return response()->json($result['body'], $result['status']);
    }
}
