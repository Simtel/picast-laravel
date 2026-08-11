<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Application\Service;

use App\Context\ChadGPT\Application\Data\ChadGptModel;
use App\Context\ChadGPT\Application\Data\ChadGptRequestData;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ChadGptRequestService
{
    private const int TIMEOUT = 60;

    private const int MODELS_TIMEOUT = 15;

    private const string CHAT_COMPLETIONS_ENDPOINT = 'chat/completions';

    private const string MODELS_ENDPOINT = 'models';

    private const string MODELS_CACHE_KEY = 'chadgpt.models';

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

        /** @var Response $response */
        $response = Http::timeout(self::TIMEOUT)
            ->withToken($this->getApiKey(), 'Bearer')
            ->post($this->endpoint(self::CHAT_COMPLETIONS_ENDPOINT), $requestData);

        return $response;
    }

    /**
     * @return array<int, ChadGptModel>
     */
    public function getModels(): array
    {
        $cached = Cache::get(self::MODELS_CACHE_KEY);

        if (is_array($cached)) {
            return array_values(array_filter(
                $cached,
                static fn (mixed $model): bool => $model instanceof ChadGptModel
            ));
        }

        $models = $this->fetchModels();

        $ttl = $models === []
            ? now()->addMinutes(5)
            : now()->addSeconds($this->getCacheTtl());

        Cache::put(self::MODELS_CACHE_KEY, $models, $ttl);

        return $models;
    }

    /**
     * @return string[]
     */
    public function getModelIds(): array
    {
        return array_map(
            static fn (ChadGptModel $model): string => $model->id,
            $this->getModels()
        );
    }

    public function getDefaultModelId(): string
    {
        $default = $this->getConfiguredDefaultModel();
        $models = $this->getModels();

        foreach ($models as $model) {
            if ($model->id === $default) {
                return $default;
            }
        }

        return $models[0]->id ?? $default;
    }

    /**
     * @return array<int, ChadGptModel>
     */
    private function fetchModels(): array
    {
        try {
            /** @var Response $response */
            $response = Http::timeout(self::MODELS_TIMEOUT)
                ->withToken($this->getApiKey(), 'Bearer')
                ->get($this->endpoint(self::MODELS_ENDPOINT));
        } catch (ConnectionException $e) {
            Log::error('ChadGPT: models fetch connection error', ['error' => $e->getMessage()]);

            return [];
        }

        if (!$response->successful()) {
            Log::error('ChadGPT: models fetch failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        }

        /** @var array<int, array{id?: string, is_old_model?: bool, owned_by?: string}> $data */
        $data = $response->json('data') ?? [];

        $defaultModel = $this->getConfiguredDefaultModel();
        $models = [];

        foreach ($data as $item) {
            $id = $item['id'] ?? null;

            if (!is_string($id) || $id === '') {
                continue;
            }

            if (($item['is_old_model'] ?? false) === true) {
                continue;
            }

            if ($this->isNonChatModel($id)) {
                continue;
            }

            $models[] = new ChadGptModel(
                id: $id,
                label: $this->buildLabel($id),
                isDefault: $id === $defaultModel,
            );
        }

        return $models;
    }

    private function isNonChatModel(string $id): bool
    {
        return str_starts_with($id, 'text-embedding')
            || str_ends_with($id, '-transcribe');
    }

    private function buildLabel(string $id): string
    {
        $vendorNames = [
            'gpt' => 'GPT',
            'gemini' => 'Gemini',
            'claude' => 'Claude',
            'deepseek' => 'DeepSeek',
            'glm' => 'GLM',
            'kimi' => 'Kimi',
            'qwen' => 'Qwen',
            'grok' => 'Grok',
        ];

        $parts = preg_split('/-/', $id) ?: [$id];

        $first = strtolower($parts[0]);
        if (isset($vendorNames[$first])) {
            $parts[0] = $vendorNames[$first];
        } else {
            $parts[0] = ucfirst($parts[0]);
        }

        for ($i = 1, $count = count($parts); $i < $count; $i++) {
            $parts[$i] = ucfirst($parts[$i]);
        }

        return implode(' ', $parts);
    }

    private function endpoint(string $suffix): string
    {
        /** @var string $baseUrl */
        $baseUrl = config('chadgpt.url');

        return rtrim($baseUrl, '/') . '/' . $suffix;
    }

    private function getConfiguredDefaultModel(): string
    {
        $default = config('chadgpt.default_model');

        return is_string($default) && $default !== '' ? $default : 'gpt-5.6-terra';
    }

    private function getCacheTtl(): int
    {
        $ttl = config('chadgpt.models_cache_ttl');

        return is_int($ttl) && $ttl > 0 ? $ttl : 86400;
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
