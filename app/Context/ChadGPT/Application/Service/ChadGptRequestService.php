<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Application\Service;

use App\Context\ChadGPT\Application\Data\ChadGptRequestData;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ChadGptRequestService
{
    private const int TIMEOUT = 60;


    /**
     * @param ChadGptRequestData $chadGptRequestData
     * @return Response
     * @throws ConnectionException
     */
    public function request(ChadGptRequestData $chadGptRequestData): Response
    {
        $requestData = [
            'message' => $chadGptRequestData->userMessage,
            'api_key' => $this->getApiKey(),
        ];

        if ($chadGptRequestData->history !== null) {
            $requestData['history'] = $chadGptRequestData->history;
        }

        if ($chadGptRequestData->temperature !== null) {
            $requestData['temperature'] = $chadGptRequestData->temperature;
        }

        if ($chadGptRequestData->maxTokens !== null) {
            $requestData['max_tokens'] = $chadGptRequestData->maxTokens;
        }

        if ($chadGptRequestData->images !== null) {
            $requestData['images'] = $chadGptRequestData->images;
        }

        $endpoint = config('chadgpt.url') . $chadGptRequestData->model;

        /** @var Response $response */
        $response = Http::timeout(self::TIMEOUT)->post($endpoint, $requestData);
        return $response;
    }

    /**
     * @return array{
     *     is_success: bool,
     *     used_words: int,
     *     total_words: int,
     *     remaining_words: int,
     *     reserved_words: int,
     *     error_code: string|null,
     *     error_message: string|null,
     * }
     */
    public function getWordsBalance(): array
    {
        $endpoint = config('chadgpt.url') . 'words';

        $response = Http::timeout(self::TIMEOUT)->post($endpoint, [
            'api_key' => $this->getApiKey(),
        ]);

        if (!$response->successful()) {
            Log::error('ChadGPT: words balance request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'is_success' => false,
                'used_words' => 0,
                'total_words' => 0,
                'remaining_words' => 0,
                'reserved_words' => 0,
                'error_code' => null,
                'error_message' => 'Не удалось получить информацию о балансе искр',
            ];
        }

        /** @var array<string, mixed> $responseData */
        $responseData = $response->json();

        if (!($responseData['is_success'] ?? false)) {
            Log::error('ChadGPT: words balance API error', $responseData);

            return [
                'is_success' => false,
                'used_words' => 0,
                'total_words' => 0,
                'remaining_words' => 0,
                'reserved_words' => 0,
                'error_code' => isset($responseData['error_code']) ? strval($responseData['error_code']) : null,
                'error_message' => isset($responseData['error_message']) ? strval($responseData['error_message']) : 'Неизвестная ошибка API',
            ];
        }

        return [
            'is_success' => true,
            'used_words' => intval($responseData['used_words'] ?? 0),
            'total_words' => intval($responseData['total_words'] ?? 0),
            'remaining_words' => intval($responseData['remaining_words'] ?? 0),
            'reserved_words' => intval($responseData['reserved_words'] ?? 0),
            'error_code' => null,
            'error_message' => null,
        ];
    }

    private function getApiKey(): string
    {
        $apiKey = config('chadgpt.api_key');
        if (!$apiKey) {
            Log::error('ChadGPT API key not configured');
            throw new RuntimeException('ChadGPT API key not set');
        }

        if (!is_string($apiKey)) {
            Log::error('API ключ ChadGPT должен быть строкой');
            throw new RuntimeException('ChadGPT API key must be a string');
        }

        return $apiKey;
    }
}
