<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Application\Service;

use App\Context\ChadGPT\Application\Dto\ChadGptRequest;
use http\Exception\RuntimeException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChadGptRequestService
{
    private const int TIMEOUT = 60;


    public function request(ChadGptRequest $chadGptRequest): Response
    {
        $requestData = [
            'message' => $chadGptRequest->getUserMessage(),
            'api_key' => $this->getApiKEy()
        ];

        $endpoint = config('chadgpt.url') . $chadGptRequest->getModel();

        return Http::timeout(self::TIMEOUT)->post($endpoint, $requestData);
    }

    private function getApiKEy(): string
    {
        $apiKey = config('chadgpt.api_key');
        if (!$apiKey) {
            Log::error('ChadGPT API key not configured');
            throw new RuntimeException('ChadGPT API key not set');
        }

        if (!is_string($apiKey)) {
            Log::error('ChadGPT API key must be a string');
            throw new RuntimeException('ChadGPT API key must be a string');
        }

        return $apiKey;
    }
}
