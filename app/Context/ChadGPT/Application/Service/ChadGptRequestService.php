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
            'api_key' => $this->getApiKEy()
        ];

        $endpoint = config('chadgpt.url') . $chadGptRequestData->model;

        /** @var Response $response */
        $response = Http::timeout(self::TIMEOUT)->post($endpoint, $requestData);
        return $response;
    }

    private function getApiKEy(): string
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
