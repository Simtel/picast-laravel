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

    private const string CHAT_COMPLETIONS_ENDPOINT = 'chat/completions';


    /**
     * @param ChadGptRequestData $chadGptRequestData
     * @return Response
     * @throws ConnectionException
     */
    public function request(ChadGptRequestData $chadGptRequestData): Response
    {
        $requestData = [
            'model' => $chadGptRequestData->model,
            'messages' => $this->buildMessages($chadGptRequestData),
        ];

        if ($chadGptRequestData->temperature !== null) {
            $requestData['temperature'] = $chadGptRequestData->temperature;
        }

        if ($chadGptRequestData->maxTokens !== null) {
            $requestData['max_tokens'] = $chadGptRequestData->maxTokens;
        }

        /** @var string $baseUrl */
        $baseUrl = config('chadgpt.url');
        $endpoint = rtrim($baseUrl, '/') . '/' . self::CHAT_COMPLETIONS_ENDPOINT;

        /** @var Response $response */
        $response = Http::timeout(self::TIMEOUT)
            ->withToken($this->getApiKey(), 'Bearer')
            ->post($endpoint, $requestData);

        return $response;
    }

    /**
     * @param ChadGptRequestData $chadGptRequestData
     * @return array<int, array{role: string, content: string|array<int, array{type: string, text?: string, image_url?: array{url: string}}>}>
     */
    private function buildMessages(ChadGptRequestData $chadGptRequestData): array
    {
        $messages = [];

        if ($chadGptRequestData->history !== null) {
            foreach ($chadGptRequestData->history as $message) {
                $messages[] = $message;
            }
        }

        $messages[] = [
            'role' => 'user',
            'content' => $this->buildUserContent($chadGptRequestData),
        ];

        return $messages;
    }

    /**
     * @param ChadGptRequestData $chadGptRequestData
     * @return string|array<int, array{type: string, text?: string, image_url?: array{url: string}}>
     */
    private function buildUserContent(ChadGptRequestData $chadGptRequestData): string|array
    {
        if ($chadGptRequestData->images === null || $chadGptRequestData->images === []) {
            return $chadGptRequestData->userMessage;
        }

        $content = [
            ['type' => 'text', 'text' => $chadGptRequestData->userMessage],
        ];

        foreach ($chadGptRequestData->images as $image) {
            $content[] = [
                'type' => 'image_url',
                'image_url' => ['url' => $image],
            ];
        }

        return $content;
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
