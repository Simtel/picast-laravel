<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Infrastructure\Controller;

use App\Common\CommandBus;
use App\Context\ChadGPT\Application\Dto\ChadGptRequest;
use App\Context\ChadGPT\Application\Service\ChadGptRequestService;
use App\Context\ChadGPT\Domain\ChatModels;
use App\Context\ChadGPT\Domain\Command\CreateChatConversationCommand;
use App\Context\ChadGPT\Infrastructure\Repository\ConversationRepository;
use App\Context\ChadGPT\Infrastructure\Repository\StatWordsUsedRepository;
use App\Context\ChadGPT\Infrastructure\Request\SendMessageRequest;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

final class ChadGptController extends Controller
{
    public function index(
        ConversationRepository $conversationRepository,
        StatWordsUsedRepository $statWordsUsedRepository
    ): View {
        $user = Auth::user();
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
        CommandBus $commandBus,
        ChadGptRequestService $chadGptRequestService
    ): JsonResponse {
        Log::info('ChadGPT: sending message', ['request' => $request->all()]);

        try {
            $chadGptRequest = new ChadGptRequest(
                $request->input('model', 'gpt-4o-mini'),
                $request->string('message')->value(),
            );

            $response = $chadGptRequestService->request($chadGptRequest);

            if (!$response->successful()) {
                Log::error('ChadGPT: API connection failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json(
                    ['error' => 'Failed to connect to ChadGPT API'],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
            /** @var array{is_success: ?bool, response: string, used_words_count: int, error_message: ?string} $data */
            $data = $response->json();

            if (!($data['is_success'] ?? false)) {
                Log::error('ChadGPT: API error response', $data);

                return response()->json(
                    ['error' => $data['error_message'] ?? 'Unknown API error'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $userWordsCount = $data['used_words_count'];

            try {
                $command = new CreateChatConversationCommand(
                    user: Auth::user(),
                    model: $chadGptRequest->getModel(),
                    userMessage: $chadGptRequest->getUserMessage(),
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
                ['error' => 'An error occurred while communicating with ChadGPT API'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function clearHistory(ConversationRepository $conversationRepository): JsonResponse
    {
        try {
            $conversationRepository->deleteByUser(Auth::user());

            return response()->json([
                'success' => true,
                'message' => 'Chat history cleared successfully',
            ]);
        } catch (Throwable $e) {
            Log::error('ChadGPT: failed to clear history', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to clear chat history',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
