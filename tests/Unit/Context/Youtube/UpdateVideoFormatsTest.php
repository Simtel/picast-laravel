<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Youtube;

use App\Context\Youtube\Application\Service\RefreshVideoFormatsService;
use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Infrastructure\Jobs\UpdateVideoFormats;
use Exception;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

final class UpdateVideoFormatsTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_update_video_formats(): void
    {
        Event::fake();

        $video = Video::factory()->create();
        Log::shouldReceive('info')->once()->with('Start download video formats:' . $video->title);

        /** @var RefreshVideoFormatsService $refreshVideoFormatsService */
        $refreshVideoFormatsService = Mockery::mock(RefreshVideoFormatsService::class)
            ->expects('refresh')
            ->with($video)
            ->getMock();

        $job = new UpdateVideoFormats($video);

        $job->handle($refreshVideoFormatsService);
    }
}
