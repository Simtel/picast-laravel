<?php

declare(strict_types=1);

namespace App\Http\Controllers\Personal\ChadGpt;

use App\Http\Controllers\Controller;
use App\Models\ChadGptConversation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class ChadGptController extends Controller
{
    /**
     * Display the ChadGPT chat page
     *
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        try {
            // Get conversation history for the current user
            $conversations = ChadGptConversation::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();
        } catch (\Exception $e) {
            Log::error('Error fetching ChadGPT conversations', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            $conversations = collect(); // Empty collection if there's an error
        }

        return view('personal.chadgpt.index', compact('conversations'));
    }

    /**
     * Send a message to ChadGPT API and return the response
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendMessage(Request $request): JsonResponse
    {
        Log::info('ChadGPT sendMessage called', ['request' => $request->all()]);

        $request->validate([
            'message' => 'required|string|max:1000',
            'model' => 'nullable|string|in:gpt-5,gpt-5-mini,gpt-5-nano,gpt-4o-mini,gpt-4o,claude-3-haiku,claude-3-opus,claude-4.5-sonnet,claude-3.7-sonnet-thinking,claude-4.1-opus,gemini-2.0-flash,gemini-2.5-pro,deepseek-v3.1'
        ]);

        $apiKey = config('chadgpt.api_key');
        if (!$apiKey) {
            Log::error('ChadGPT API key not configured');
            return response()->json([
                'error' => 'API key not configured. Please set CHADGPT_API_KEY in your .env file.'
            ], 400);
        }

        /** @var string $model */
        $model = $request->input('model', 'gpt-4o-mini');
        $userMessage = $request->input('message');
        $endpoint = "https://ask.chadgpt.ru/api/public/{$model}";

        Log::info('ChadGPT API request', [
            'endpoint' => $endpoint,
            'model' => $model,
            'message' => $userMessage
        ]);

        $requestData = [
            'message' => $userMessage,
            'api_key' => $apiKey
        ];

        try {
            $response = Http::timeout(30)->post($endpoint, $requestData);

            Log::info('ChadGPT API response', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            if ($response->successful()) {
                /** @var mixed[] $responseData */
                $responseData = $response->json();

                if ($responseData['is_success']) {
                    try {
                        // Save conversation to database
                        ChadGptConversation::create([
                            'user_id' => Auth::id(),
                            'model' => $model,
                            'user_message' => $userMessage,
                            'ai_response' => $responseData['response'],
                            'used_words_count' => $responseData['used_words_count'] ?? 0,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Error saving ChadGPT conversation to database', [
                            'error' => $e->getMessage(),
                            'user_id' => Auth::id(),
                            'model' => $model
                        ]);
                        // We don't return an error here because the API call was successful
                        // We just log the database error and continue
                    }

                    return response()->json([
                        'success' => true,
                        'response' => $responseData['response'],
                        'used_words_count' => $responseData['used_words_count'] ?? 0
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
        } catch (\Exception $e) {
            Log::error('ChadGPT API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'An error occurred while communicating with ChadGPT API: ' . $e->getMessage()
            ], 500);
        }
    }
}
