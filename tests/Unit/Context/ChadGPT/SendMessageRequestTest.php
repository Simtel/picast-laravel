<?php

declare(strict_types=1);

namespace Tests\Unit\Context\ChadGPT;

use App\Context\ChadGPT\Application\Service\ChadGptRequestService;
use App\Context\ChadGPT\Infrastructure\Request\SendMessageRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Tests\TestCase;

class SendMessageRequestTest extends TestCase
{
    public function test_authorize_returns_true(): void
    {
        $request = new SendMessageRequest();
        $request->setContainer(app());

        $this->assertTrue($request->authorize());
    }

    public function test_rules_include_model_in_rule_when_models_exist(): void
    {
        $service = $this->getMockBuilder(ChadGptRequestService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $service->method('getModelIds')->willReturn(['gpt-5.6-terra', 'claude-5-sonnet']);

        app()->instance(ChadGptRequestService::class, $service);

        $request = new SendMessageRequest();
        $request->setContainer(app());
        $rules = $request->rules();

        /** @var array{0: string, 1: string, 2: string, 3: \Illuminate\Validation\Rules\In} $modelRules */
        $modelRules = $rules['model'];

        $this->assertSame([
            'nullable',
            'string',
            'max:100',
        ], array_slice($modelRules, 0, 3));
        $this->assertInstanceOf(\Illuminate\Validation\Rules\In::class, $modelRules[3]);
        $this->assertSame('required|string|max:1000', $rules['message']);
        $this->assertSame('nullable|numeric|between:0,2', $rules['temperature']);
        $this->assertSame('nullable|integer|min:1', $rules['max_tokens']);
        $this->assertSame('nullable|array|max:5', $rules['images']);
        $this->assertSame('nullable|string', $rules['images.*']);
    }

    public function test_rules_omit_model_in_rule_when_no_models_available(): void
    {
        $service = $this->getMockBuilder(ChadGptRequestService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $service->method('getModelIds')->willReturn([]);

        app()->instance(ChadGptRequestService::class, $service);

        $request = new SendMessageRequest();
        $request->setContainer(app());
        $rules = $request->rules();

        $this->assertSame([
            'nullable',
            'string',
            'max:100',
        ], $rules['model']);
    }

    public function test_failed_validation_throws_http_response_exception(): void
    {
        $service = $this->getMockBuilder(ChadGptRequestService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $service->method('getModelIds')->willReturn(['gpt-5.6-terra']);

        app()->instance(ChadGptRequestService::class, $service);

        $request = SendMessageRequest::create('/personal/chadgpt/send-message', 'POST', [
            'message' => '',
        ]);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));

        try {
            $request->validateResolved();
            $this->fail('Expected HttpResponseException was not thrown.');
        } catch (HttpResponseException $e) {
            $this->assertSame(422, $e->getResponse()->getStatusCode());
            /** @var array{errors: array<string, array<int, string>>} $decoded */
            $decoded = json_decode($e->getResponse()->getContent(), true);
            $this->assertArrayHasKey('errors', $decoded);
            $this->assertArrayHasKey('message', $decoded['errors']);
        }
    }

    public function test_failed_validation_wraps_validation_exception(): void
    {
        $service = $this->getMockBuilder(ChadGptRequestService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $service->method('getModelIds')->willReturn(['gpt-5.6-terra']);

        app()->instance(ChadGptRequestService::class, $service);

        $request = SendMessageRequest::create('/personal/chadgpt/send-message', 'POST', [
            'message' => str_repeat('a', 2000),
        ]);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));

        try {
            $request->validateResolved();
            $this->fail('Expected HttpResponseException was not thrown.');
        } catch (HttpResponseException $e) {
            /** @var array{errors: array<string, array<int, string>>} $decoded */
            $decoded = json_decode($e->getResponse()->getContent(), true);
            $this->assertNotEmpty($decoded['errors']['message']);
        }
    }
}
