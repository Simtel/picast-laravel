<?php

declare(strict_types=1);

namespace Tests\Unit\Common;

use App\Exceptions\Handler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class ExceptionHandlerTest extends TestCase
{
    public function test_render(): void
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('wantsJson')->andReturn(true);
        $handler = new Handler(app());

        $response = $handler->render($request, new ModelNotFoundException());

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(404, $response->getStatusCode());
        self::assertJson((string)$response->getContent());
        self::assertEquals('{"data":"Resource not found"}', (string)$response->getContent());
    }

    public function test_unauthenticated(): void
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('wantsJson')->andReturn(false);
        $request->shouldReceive('expectsJson')->andReturn(false);
        $handler = new Handler(app());

        $response = $handler->render($request, new AuthenticationException());

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals(302, $response->getStatusCode());
        self::assertEquals(route('login'), $response->getTargetUrl());
    }
}
