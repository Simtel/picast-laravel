<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Infrastructure\Http\Controllers;

use App\Context\ChadGPT\Application\Data\ChadGptRequestData;
use App\Context\ChadGPT\Application\Service\ChadGptRequestService;
use App\Context\ChadGPT\Application\Service\SendChatMessageService;
use App\Context\ChadGPT\Infrastructure\Repository\ConversationRepository;
use App\Context\ChadGPT\Infrastructure\Repository\StatWordsUsedRepository;
use App\Context\ChadGPT\Infrastructure\Request\SendMessageRequest;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

final class ChadGptController extends Controller
{
    public function index(
        Request $request,
        ChadGptRequestService $chadGptRequestService,
        ConversationRepository $conversationRepository,
        StatWordsUsedRepository $statWordsUsedRepository,
    ): View {
        $user = $request->user();
        $wordStats = $statWordsUsedRepository->findByUser($user);

        return view('personal.chadgpt.index', [
            'models' => $chadGptRequestService->getModels(),
            'conversations' => $conversationRepository->findBuUser($user),
            'word_stats' => $wordStats,
            'word_stats_sum' => $wordStats->sum(static fn ($stat) => $stat->getTokensUsed()),
        ]);
    }

    public function sendMessage(
        SendMessageRequest $request,
        ChadGptRequestService $chadGptRequestService,
        SendChatMessageService $sendChatMessageService,
    ): JsonResponse {
        Log::info('ChadGPT: sending message', ['request' => $request->all()]);

        try {
            $chadGptRequestData = ChadGptRequestData::from([
                'model' => $request->filled('model')
                    ? $request->input('model')
                    : $chadGptRequestService->getDefaultModelId(),
                'userMessage' => $request->string('message')->value(),
                'temperature' => $request->filled('temperature') ? $request->float('temperature') : null,
                'maxTokens' => $request->filled('max_tokens') ? $request->integer('max_tokens') : null,
                'images' => $request->input('images'),
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
                'used_tokens_count' => $result['used_tokens_count'],
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
