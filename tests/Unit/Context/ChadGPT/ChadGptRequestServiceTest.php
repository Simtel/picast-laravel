<?php

declare(strict_types=1);

namespace Tests\Unit\Context\ChadGPT;

use App\Context\ChadGPT\Application\Data\ChadGptModel;
use App\Context\ChadGPT\Application\Data\ChadGptRequestData;
use App\Context\ChadGPT\Application\Service\ChadGptRequestService;
use Config;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Log;
use RuntimeException;
use Tests\TestCase;

class ChadGptRequestServiceTest extends TestCase
{
    private const string ENDPOINT = 'https://api.chadgpt.com/chat/completions';

    private ChadGptRequestService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->service = new ChadGptRequestService();
    }

    public function testRequestSuccessfully(): void
    {
        $message = 'Test message';
        $model = 'gpt-5.6-terra';
        $apiKey = 'test-api-key';

        Config::set('chadgpt.api_key', $apiKey);
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        $chadGptRequestData = ChadGptRequestData::from([
            'model' => $model,
            'userMessage' => $message,
        ]);

        Http::fake([
            self::ENDPOINT => Http::response(['choices' => [['message' => ['content' => 'success']]]], 200)
        ]);

        $response = $this->service->request($chadGptRequestData);

        $this->assertEquals(200, $response->status());
        $this->assertEquals(['choices' => [['message' => ['content' => 'success']]]], $response->json());

        Http::assertSent(static function (Request $request) use ($model, $message, $apiKey) {
            $data = $request->data();

            return $request->url() === self::ENDPOINT
                && $request->hasHeader('Authorization', 'Bearer ' . $apiKey)
                && $data['model'] === $model
                && $data['messages'] === [
                    ['role' => 'user', 'content' => $message],
                ];
        });
    }

    public function testRequestThrowsConnectionException(): void
    {
        $apiKey = 'test-api-key';

        Config::set('chadgpt.api_key', $apiKey);
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        $chadGptRequestData = ChadGptRequestData::from([
            'model' => 'gpt-5.6-terra',
            'userMessage' => 'Test',
        ]);

        Http::fake([
            self::ENDPOINT => static function () {
                throw new ConnectionException('Connection failed');
            }
        ]);

        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage('Connection failed');

        $this->service->request($chadGptRequestData);
    }

    public function testGetApiKeyThrowsExceptionWhenNotConfigured(): void
    {
        Config::set('chadgpt.api_key', null);
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        $chadGptRequestData = ChadGptRequestData::from([
            'model' => 'gpt-5.6-terra',
            'userMessage' => 'Test',
        ]);

        Log::shouldReceive('error')
            ->once()
            ->with('ChadGPT API key not configured');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ChadGPT API key not set');

        $this->service->request($chadGptRequestData);
    }

    public function testGetApiKeyThrowsExceptionWhenNotString(): void
    {
        Config::set('chadgpt.api_key', 12345);
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        $chadGptRequestData = ChadGptRequestData::from([
            'model' => 'gpt-5.6-terra',
            'userMessage' => 'Test',
        ]);

        Log::shouldReceive('error')
            ->once()
            ->with('API ключ ChadGPT должен быть строкой');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ChadGPT API key must be a string');

        $this->service->request($chadGptRequestData);
    }

    public function testGetApiKeyThrowsExceptionWhenEmptyString(): void
    {
        Config::set('chadgpt.api_key', '');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        $chadGptRequestData = ChadGptRequestData::from([
            'model' => 'gpt-5.6-terra',
            'userMessage' => 'Test',
        ]);

        Log::shouldReceive('error')
            ->once()
            ->with('ChadGPT API key not configured');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ChadGPT API key not set');

        $this->service->request($chadGptRequestData);
    }

    public function testRequestBuildsCorrectEndpoint(): void
    {
        Config::set('chadgpt.api_key', 'test-key');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/v1/');

        $chadGptRequestData = ChadGptRequestData::from([
            'model' => 'gpt-5.6-terra',
            'userMessage' => 'Hello',
        ]);

        Http::fake([
            'https://api.chadgpt.com/v1/chat/completions' => Http::response(['data' => 'test'], 200)
        ]);

        $this->service->request($chadGptRequestData);

        Http::assertSent(static function (Request $request) {
            return $request->url() === 'https://api.chadgpt.com/v1/chat/completions';
        });
    }

    public function testRequestSendsMessagesWithHistory(): void
    {
        $message = 'What is the weather?';

        Config::set('chadgpt.api_key', 'secret-api-key-123');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        $chadGptRequestData = ChadGptRequestData::from([
            'model' => 'gpt-5.6-terra',
            'userMessage' => $message,
            'history' => [
                ['role' => 'user', 'content' => 'Previous question'],
                ['role' => 'assistant', 'content' => 'Previous answer'],
            ],
        ]);

        Http::fake();

        $this->service->request($chadGptRequestData);

        Http::assertSent(static function (Request $request) use ($message) {
            $data = $request->data();

            return $data['model'] === 'gpt-5.6-terra'
                && $data['messages'] === [
                    ['role' => 'user', 'content' => 'Previous question'],
                    ['role' => 'assistant', 'content' => 'Previous answer'],
                    ['role' => 'user', 'content' => $message],
                ];
        });
    }

    public function testRequestSendsOptionalParams(): void
    {
        Config::set('chadgpt.api_key', 'secret-api-key-123');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        $chadGptRequestData = ChadGptRequestData::from([
            'model' => 'gpt-5.6-terra',
            'userMessage' => 'Hello',
            'temperature' => 0.7,
            'maxTokens' => 500,
        ]);

        Http::fake();

        $this->service->request($chadGptRequestData);

        Http::assertSent(static function (Request $request) {
            $data = $request->data();

            return $data['temperature'] === 0.7
                && $data['max_tokens'] === 500
                && $data['model'] === 'gpt-5.6-terra';
        });
    }

    public function testRequestSendsImagesAsContentParts(): void
    {
        Config::set('chadgpt.api_key', 'secret-api-key-123');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        $chadGptRequestData = ChadGptRequestData::from([
            'model' => 'gpt-5.6-terra',
            'userMessage' => 'Опиши, что на изображении.',
            'images' => ['https://example.com/image.jpg'],
        ]);

        Http::fake();

        $this->service->request($chadGptRequestData);

        Http::assertSent(static function (Request $request) {
            $data = $request->data();

            return $data['messages'] === [
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => 'Опиши, что на изображении.'],
                        ['type' => 'image_url', 'image_url' => ['url' => 'https://example.com/image.jpg']],
                    ],
                ],
            ];
        });
    }

    public function testRequestDoesNotSendNullOptionalParams(): void
    {
        Config::set('chadgpt.api_key', 'test-api-key');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        $chadGptRequestData = ChadGptRequestData::from([
            'model' => 'gpt-5.6-terra',
            'userMessage' => 'Hello',
        ]);

        Http::fake();

        $this->service->request($chadGptRequestData);

        Http::assertSent(static function (Request $request) {
            $data = $request->data();

            return !array_key_exists('temperature', $data)
                && !array_key_exists('max_tokens', $data)
                && isset($data['messages']);
        });
    }

    public function testGetModelsFetchesAndParsesModels(): void
    {
        Config::set('chadgpt.api_key', 'test-api-key');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        Http::fake([
            'https://api.chadgpt.com/models' => Http::response([
                'object' => 'list',
                'data' => [
                    ['id' => 'gpt-5.6-terra', 'object' => 'model', 'is_old_model' => false],
                    ['id' => 'claude-5-sonnet', 'object' => 'model', 'is_old_model' => false],
                    ['id' => 'gpt-4.1', 'object' => 'model', 'is_old_model' => true],
                    ['id' => 'text-embedding-3-small', 'object' => 'model', 'is_old_model' => false],
                    ['id' => 'gpt-4o-mini-transcribe', 'object' => 'model', 'is_old_model' => false],
                ],
            ], 200),
        ]);

        $models = $this->service->getModels();

        $this->assertCount(2, $models);
        $this->assertSame('gpt-5.6-terra', $models[0]->id);
        $this->assertSame('GPT 5.6 Terra', $models[0]->label);
        $this->assertTrue($models[0]->isDefault);
        $this->assertSame('claude-5-sonnet', $models[1]->id);
        $this->assertFalse($models[1]->isDefault);

        Http::assertSent(static function (Request $request) {
            return $request->url() === 'https://api.chadgpt.com/models'
                && $request->hasHeader('Authorization', 'Bearer test-api-key');
        });
    }

    public function testGetModelsReturnsEmptyOnApiFailure(): void
    {
        Config::set('chadgpt.api_key', 'test-api-key');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        Http::fake([
            'https://api.chadgpt.com/models' => Http::response([], 500),
        ]);

        $this->assertSame([], $this->service->getModels());
    }

    public function testGetModelsReturnsEmptyOnConnectionError(): void
    {
        Config::set('chadgpt.api_key', 'test-api-key');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        Http::fake([
            'https://api.chadgpt.com/models' => static function () {
                throw new ConnectionException('Connection failed');
            },
        ]);

        $this->assertSame([], $this->service->getModels());
    }

    public function testGetDefaultModelIdFallsBackToConfiguredDefault(): void
    {
        Config::set('chadgpt.api_key', 'test-api-key');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');
        Config::set('chadgpt.default_model', 'gpt-5-mini');

        Http::fake([
            'https://api.chadgpt.com/models' => Http::response([
                'object' => 'list',
                'data' => [
                    ['id' => 'claude-5-sonnet', 'object' => 'model', 'is_old_model' => false],
                ],
            ], 200),
        ]);

        $this->assertSame('claude-5-sonnet', $this->service->getDefaultModelId());
    }

    public function testGetDefaultModelIdReturnsDefaultWhenAvailable(): void
    {
        Config::set('chadgpt.api_key', 'test-api-key');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');
        Config::set('chadgpt.default_model', 'gpt-5.6-terra');

        Http::fake([
            'https://api.chadgpt.com/models' => Http::response([
                'object' => 'list',
                'data' => [
                    ['id' => 'gpt-5.6-terra', 'object' => 'model', 'is_old_model' => false],
                    ['id' => 'claude-5-sonnet', 'object' => 'model', 'is_old_model' => false],
                ],
            ], 200),
        ]);

        $this->assertSame('gpt-5.6-terra', $this->service->getDefaultModelId());
    }

    public function testGetDefaultModelIdReturnsConfiguredDefaultWhenNoModels(): void
    {
        Config::set('chadgpt.api_key', 'test-api-key');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');
        Config::set('chadgpt.default_model', 'gpt-5-mini');

        Http::fake([
            'https://api.chadgpt.com/models' => Http::response([
                'object' => 'list',
                'data' => [],
            ], 200),
        ]);

        $this->assertSame('gpt-5-mini', $this->service->getDefaultModelId());
    }

    public function testGetDefaultModelIdFallsBackToHardcodedDefault(): void
    {
        Config::set('chadgpt.api_key', 'test-api-key');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');
        Config::set('chadgpt.default_model', '');

        Http::fake([
            'https://api.chadgpt.com/models' => Http::response([
                'object' => 'list',
                'data' => [],
            ], 200),
        ]);

        $this->assertSame('gpt-5.6-terra', $this->service->getDefaultModelId());
    }

    public function testGetModelIdsReturnsIdsFromModels(): void
    {
        Config::set('chadgpt.api_key', 'test-api-key');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        Http::fake([
            'https://api.chadgpt.com/models' => Http::response([
                'object' => 'list',
                'data' => [
                    ['id' => 'gpt-5.6-terra', 'object' => 'model', 'is_old_model' => false],
                    ['id' => 'claude-5-sonnet', 'object' => 'model', 'is_old_model' => false],
                ],
            ], 200),
        ]);

        $this->assertSame(['gpt-5.6-terra', 'claude-5-sonnet'], $this->service->getModelIds());
    }

    public function testGetModelsReturnsCachedModelsWithoutApiCall(): void
    {
        Config::set('chadgpt.api_key', 'test-api-key');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        $cachedModels = [
            new ChadGptModel(id: 'gpt-5.6-terra', label: 'GPT 5.6 Terra', isDefault: true),
            new ChadGptModel(id: 'claude-5-sonnet', label: 'Claude 5 Sonnet', isDefault: false),
        ];

        Cache::put('chadgpt.models', $cachedModels, now()->addMinutes(10));

        Http::fake();

        $models = $this->service->getModels();

        $this->assertCount(2, $models);
        $this->assertSame('gpt-5.6-terra', $models[0]->id);
        $this->assertSame('claude-5-sonnet', $models[1]->id);

        Http::assertNothingSent();
    }

    public function testGetModelsFiltersInvalidEntriesFromCache(): void
    {
        Config::set('chadgpt.api_key', 'test-api-key');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        Cache::put('chadgpt.models', [
            new ChadGptModel(id: 'gpt-5.6-terra', label: 'GPT 5.6 Terra'),
            'not-a-model',
            123,
        ], now()->addMinutes(10));

        Http::fake();

        $models = $this->service->getModels();

        $this->assertCount(1, $models);
        $this->assertSame('gpt-5.6-terra', $models[0]->id);
    }

    public function testGetModelsStoresEmptyArrayInCacheOnFailure(): void
    {
        Config::set('chadgpt.api_key', 'test-api-key');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        Http::fake([
            'https://api.chadgpt.com/models' => Http::response([], 500),
        ]);

        $this->assertSame([], $this->service->getModels());

        $cached = Cache::get('chadgpt.models');
        $this->assertIsArray($cached);
        $this->assertSame([], $cached);
    }

    public function testGetModelsSkipsModelsWithMissingOrEmptyId(): void
    {
        Config::set('chadgpt.api_key', 'test-api-key');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        Http::fake([
            'https://api.chadgpt.com/models' => Http::response([
                'object' => 'list',
                'data' => [
                    ['object' => 'model', 'is_old_model' => false],
                    ['id' => '', 'object' => 'model', 'is_old_model' => false],
                    ['id' => 'valid-model', 'object' => 'model', 'is_old_model' => false],
                ],
            ], 200),
        ]);

        $models = $this->service->getModels();

        $this->assertCount(1, $models);
        $this->assertSame('valid-model', $models[0]->id);
    }

    public function testGetModelsBuildsLabelsForKnownVendors(): void
    {
        Config::set('chadgpt.api_key', 'test-api-key');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        Http::fake([
            'https://api.chadgpt.com/models' => Http::response([
                'object' => 'list',
                'data' => [
                    ['id' => 'gemini-2.5-flash', 'object' => 'model', 'is_old_model' => false],
                    ['id' => 'claude-5-sonnet', 'object' => 'model', 'is_old_model' => false],
                    ['id' => 'deepseek-v3', 'object' => 'model', 'is_old_model' => false],
                    ['id' => 'unknown-model', 'object' => 'model', 'is_old_model' => false],
                ],
            ], 200),
        ]);

        $models = $this->service->getModels();

        $this->assertSame('Gemini 2.5 Flash', $models[0]->label);
        $this->assertSame('Claude 5 Sonnet', $models[1]->label);
        $this->assertSame('DeepSeek V3', $models[2]->label);
        $this->assertSame('Unknown Model', $models[3]->label);
    }

    public function testGetModelsUsesConfiguredCacheTtl(): void
    {
        Config::set('chadgpt.api_key', 'test-api-key');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');
        Config::set('chadgpt.models_cache_ttl', 3600);

        Http::fake([
            'https://api.chadgpt.com/models' => Http::response([
                'object' => 'list',
                'data' => [
                    ['id' => 'gpt-5.6-terra', 'object' => 'model', 'is_old_model' => false],
                ],
            ], 200),
        ]);

        $this->service->getModels();

        $cacheKey = Cache::get('chadgpt.models');
        $this->assertIsArray($cacheKey);
        $this->assertCount(1, $cacheKey);
    }
}
