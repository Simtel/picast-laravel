<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Infrastructure\Http\Controllers;

use App\Context\ChadGPT\Application\Data\ChadGptRequestData;
use App\Context\ChadGPT\Application\Service\SendChatMessageService;
use App\Context\ChadGPT\Domain\ChatModels;
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
        ConversationRepository $conversationRepository,
        StatWordsUsedRepository $statWordsUsedRepository
    ): View {
        $user = $request->user();
        $wordStats = $statWordsUsedRepository->findByUser($user);

        return view('personal.chadgpt.index', [
            'models' => ChatModels::cases(),
            'conversations' => $conversationRepository->findBuUser($user),
            'word_stats' => $wordStats,
            'word_stats_sum' => $wordStats->sum(static fn ($stat) => $stat->getWordsUsed()),
        ]);
    }

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
