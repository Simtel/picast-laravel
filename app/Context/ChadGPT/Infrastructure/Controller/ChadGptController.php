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
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final class ChadGptController extends Controller
{
    /**
     * Display the ChadGPT chat page
     *
     * @param ConversationRepository $conversationRepository
     * @param StatWordsUsedRepository $statWordsUsedRepository
     * @return Application|Factory|View
     */
    public function index(
        ConversationRepository $conversationRepository,
        StatWordsUsedRepository $statWordsUsedRepository
    ): View|Factory|Application {
        return view('personal.chadgpt.index', [
            'conversations' => $conversationRepository->findBuUser(Auth::user()),
            'word_stats' => $statWordsUsedRepository->findByUser(Auth::user()),
        ]);
    }

    /**
     * Send a message to ChadGPT API and return the response
     *
     * @param SendMessageRequest $request
     * @param CommandBus $commandBus
     * @param ChadGptRequestService $chadGptRequestService
     * @return JsonResponse
     */
    public function sendMessage(
        SendMessageRequest $request,
        CommandBus $commandBus,
        ChadGptRequestService $chadGptRequestService
    ): JsonResponse {
        Log::info('ChadGPT sendMessage called', ['request' => $request->all()]);

        try {
            $chadGptRequest = new ChadGptRequest(
                $request->input('model', ChatModels::GPT_4O_MINI),
                $request->input('message'),
            );


            $response = $chadGptRequestService->request($chadGptRequest);

            if ($response->successful()) {
                /**
                 * @var array{
                 *     is_success: bool,
                 *     response: string,
                 *     used_words_count: int,
                 *     used_tokens_count: int,
                 *     error_code: ?string,
                 *     error_message: ?string,
                 * } $responseData
                 */

                $responseData = $response->json();

                if ($responseData['is_success']) {
                    $userWordsCount = $responseData['used_words_count'];
                    try {
                        $command = new CreateChatConversationCommand(
                            Auth::user(),
                            $chadGptRequest->getModel(),
                            $chadGptRequest->getUserMessage(),
                            $responseData['response'],
                            $userWordsCount,
                        );
                        $commandBus->execute($command);
                    } catch (Exception $e) {
                        Log::error('Error saving ChadGPT conversation to database', [
                            'error' => $e->getMessage(),
                            'user_id' => Auth::id(),
                            'model' => $chadGptRequest->getModel(),
                        ]);
                    }

                    return response()->json([
                        'success' => true,
                        'response' => $responseData['response'],
                        'used_words_count' => $userWordsCount,
                    ]);
                }

                Log::error('ChadGPT API error response', $responseData);
                return response()->json([
                    'error' => $responseData['error_message'] ?? 'Unknown error from ChadGPT API'
                ], 400);
            }

            Log::error('ChadGPT API connection failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return response()->json([
                'error' => 'Failed to connect to ChadGPT API. Status code: ' . $response->status()
            ], 500);
        } catch (Exception $e) {
            Log::error('ChadGPT API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'An error occurred while communicating with ChadGPT API: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clearHistory(ConversationRepository $conversationRepository): JsonResponse
    {
        try {
            $conversationRepository->deleteByUser(Auth::user());

            return response()->json([
                'success' => true,
                'message' => 'Chat history cleared successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Error clearing ChadGPT chat history', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to clear chat history'
            ], 500);
        }
    }
}
