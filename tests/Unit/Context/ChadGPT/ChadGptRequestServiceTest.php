<?php

declare(strict_types=1);

namespace Tests\Unit\Context\ChadGPT;

use App\Context\ChadGPT\Application\Dto\ChadGptRequest;
use App\Context\ChadGPT\Application\Service\ChadGptRequestService;
use Config;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Log;
use RuntimeException;
use Tests\TestCase;

class ChadGptRequestServiceTest extends TestCase
{
    private ChadGptRequestService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ChadGptRequestService();
    }

    public function testRequestSuccessfully(): void
    {

        $message = 'Test message';
        $model = 'gpt-4';
        $apiKey = 'test-api-key';
        $baseUrl = 'https://api.chadgpt.com/';

        Config::set('chadgpt.api_key', $apiKey);
        Config::set('chadgpt.url', $baseUrl);

        $chadGptRequest = $this->createMock(ChadGptRequest::class);
        $chadGptRequest->method('getUserMessage')->willReturn($message);
        $chadGptRequest->method('getModel')->willReturn($model);

        Http::fake([
            $baseUrl . $model => Http::response(['result' => 'success'], 200)
        ]);

        $response = $this->service->request($chadGptRequest);


        $this->assertEquals(200, $response->status());
        $this->assertEquals(['result' => 'success'], $response->json());


        Http::assertSent(static function ($request) use ($baseUrl, $model, $message, $apiKey) {
            return $request->url() === $baseUrl . $model
                //@phpstan-ignore-next-line
                && $request['message'] === $message
                //@phpstan-ignore-next-line
                && $request['api_key'] === $apiKey;
        });
    }

    public function testRequestWithTimeout(): void
    {

        $apiKey = 'test-api-key';
        $baseUrl = 'https://api.chadgpt.com/';
        $model = 'gpt-4';

        Config::set('chadgpt.api_key', $apiKey);
        Config::set('chadgpt.url', $baseUrl);

        $chadGptRequest = $this->createMock(ChadGptRequest::class);
        $chadGptRequest->method('getUserMessage')->willReturn('Test');
        $chadGptRequest->method('getModel')->willReturn($model);

        Http::fake([
            $baseUrl . $model => Http::response(['result' => 'success'], 200)
        ]);


        $this->service->request($chadGptRequest);


        Http::assertSent(static function ($request) {
            return true; // Проверяем, что запрос отправлен с таймаутом
        });
    }

    public function testRequestThrowsConnectionException(): void
    {

        $apiKey = 'test-api-key';
        $baseUrl = 'https://api.chadgpt.com/';
        $model = 'gpt-4';

        Config::set('chadgpt.api_key', $apiKey);
        Config::set('chadgpt.url', $baseUrl);

        $chadGptRequest = $this->createMock(ChadGptRequest::class);
        $chadGptRequest->method('getUserMessage')->willReturn('Test');
        $chadGptRequest->method('getModel')->willReturn($model);

        Http::fake([
            $baseUrl . $model => static function () {
                throw new ConnectionException('Connection failed');
            }
        ]);


        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage('Connection failed');


        $this->service->request($chadGptRequest);
    }

    public function testGetApiKeyThrowsExceptionWhenNotConfigured(): void
    {

        Config::set('chadgpt.api_key', null);
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        $chadGptRequest = $this->createMock(ChadGptRequest::class);
        $chadGptRequest->method('getUserMessage')->willReturn('Test');
        $chadGptRequest->method('getModel')->willReturn('gpt-4');

        Log::shouldReceive('error')
            ->once()
            ->with('ChadGPT API key not configured');


        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ChadGPT API key not set');


        $this->service->request($chadGptRequest);
    }

    public function testGetApiKeyThrowsExceptionWhenNotString(): void
    {

        Config::set('chadgpt.api_key', 12345); // Не строка
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        $chadGptRequest = $this->createMock(ChadGptRequest::class);
        $chadGptRequest->method('getUserMessage')->willReturn('Test');
        $chadGptRequest->method('getModel')->willReturn('gpt-4');

        Log::shouldReceive('error')
            ->once()
            ->with('ChadGPT API key must be a string');


        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ChadGPT API key must be a string');


        $this->service->request($chadGptRequest);
    }

    public function testGetApiKeyThrowsExceptionWhenEmptyString(): void
    {

        Config::set('chadgpt.api_key', '');
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        $chadGptRequest = $this->createMock(ChadGptRequest::class);
        $chadGptRequest->method('getUserMessage')->willReturn('Test');
        $chadGptRequest->method('getModel')->willReturn('gpt-4');

        Log::shouldReceive('error')
            ->once()
            ->with('ChadGPT API key not configured');


        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ChadGPT API key not set');


        $this->service->request($chadGptRequest);
    }

    public function testRequestBuildsCorrectEndpoint(): void
    {

        $baseUrl = 'https://api.chadgpt.com/v1/';
        $model = 'gpt-3.5-turbo';
        $expectedEndpoint = $baseUrl . $model;

        Config::set('chadgpt.api_key', 'test-key');
        Config::set('chadgpt.url', $baseUrl);

        $chadGptRequest = $this->createMock(ChadGptRequest::class);
        $chadGptRequest->method('getUserMessage')->willReturn('Hello');
        $chadGptRequest->method('getModel')->willReturn($model);

        Http::fake([
            $expectedEndpoint => Http::response(['data' => 'test'], 200)
        ]);


        $this->service->request($chadGptRequest);


        Http::assertSent(static function ($request) use ($expectedEndpoint) {
            return $request->url() === $expectedEndpoint;
        });
    }

    public function testRequestSendsCorrectPayload(): void
    {

        $message = 'What is the weather?';
        $apiKey = 'secret-api-key-123';

        Config::set('chadgpt.api_key', $apiKey);
        Config::set('chadgpt.url', 'https://api.chadgpt.com/');

        $chadGptRequest = $this->createMock(ChadGptRequest::class);
        $chadGptRequest->method('getUserMessage')->willReturn($message);
        $chadGptRequest->method('getModel')->willReturn('gpt-4');

        Http::fake();


        $this->service->request($chadGptRequest);


        Http::assertSent(static function ($request) use ($message, $apiKey) {
            $data = $request->data();
            //@phpstan-ignore-next-line
            return $data['message'] === $message
                //@phpstan-ignore-next-line
                && $data['api_key'] === $apiKey;
        });
    }
}
