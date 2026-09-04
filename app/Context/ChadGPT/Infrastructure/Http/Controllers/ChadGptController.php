<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Infrastructure\Http\Controllers;

use App\Context\ChadGPT\Application\Service\ChatService;
use App\Context\ChadGPT\Infrastructure\Request\SendMessageRequest;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ChadGptController extends Controller
{
    public function __construct(
        private readonly ChatService $chatService,
    ) {
    }

    public function index(Request $request): View
    {
        return view('personal.chadgpt.index', $this->chatService->conversationData($request->user()));
    }

    public function sendMessage(SendMessageRequest $request): JsonResponse
    {
        $result = $this->chatService->sendMessage($request->user(), $request->toData());

        return response()->json($result['body'], $result['status']);
    }

    public function clearHistory(Request $request): JsonResponse
    {
        $result = $this->chatService->clearHistory($request->user());

        return response()->json($result['body'], $result['status']);
    }
}
