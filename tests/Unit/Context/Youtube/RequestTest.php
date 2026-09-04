<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Youtube;

use App\Context\Youtube\Infrastructure\Request\QueueDownloadRequest;
use App\Context\Youtube\Infrastructure\Request\StoreVideoRequest;
use App\Context\Youtube\Infrastructure\Request\UpdateVideoRequest;
use App\Context\Youtube\Infrastructure\Request\YouTubeUrlRequest;
use Tests\TestCase;

final class RequestTest extends TestCase
{
    public function test_store_video_request(): void
    {
        $request = new StoreVideoRequest();
        $request->setContainer(app());

        self::assertTrue($request->authorize());
        self::assertSame(['url' => ['required', 'string', 'url']], $request->rules());
    }

    public function test_update_video_request(): void
    {
        $request = new UpdateVideoRequest();
        $request->setContainer(app());

        self::assertTrue($request->authorize());
        self::assertSame([
            'title' => ['sometimes', 'string'],
            'description' => ['sometimes', 'string'],
        ], $request->rules());
    }

    public function test_queue_download_request(): void
    {
        $request = new QueueDownloadRequest();
        $request->setContainer(app());

        self::assertTrue($request->authorize());
        self::assertSame([
            'format_id' => ['required', 'integer', 'exists:youtube_videos_formats,id'],
        ], $request->rules());
    }

    public function test_youtube_url_request(): void
    {
        $request = new YouTubeUrlRequest();
        $request->setContainer(app());

        self::assertTrue($request->authorize());

        self::assertSame([
            'url' => ['required', 'string', 'regex:/^(https?\:\/\/)?(www\.youtube\.com|youtu\.?be)\/.+$/'],
        ], $request->rules());

        self::assertSame([
            'url.required' => 'Поле url является обязательным.',
            'url.string' => 'Поле url должно быть строкой.',
            'url.regex' => 'Поле url должно содержать валидную ссылку на видео YouTube.',
        ], $request->messages());
    }
}
