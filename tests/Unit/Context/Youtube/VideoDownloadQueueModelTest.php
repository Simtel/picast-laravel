<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Youtube;

use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Domain\Model\VideoDownloadQueue;
use App\Context\Youtube\Domain\Model\VideoFormats;
use App\Context\Youtube\Domain\Model\VideoStatus;
use Event;
use Tests\TestCase;

final class VideoDownloadQueueModelTest extends TestCase
{
    public function test_getters(): void
    {
        Event::fake();

        $video = Video::factory()->create();
        $format = VideoFormats::factory()->create(['video_id' => $video->getId()]);

        $queue = VideoDownloadQueue::create([
            'video_id' => $video->getId(),
            'format_id' => $format->getId(),
        ]);

        self::assertEquals($queue->id, $queue->getId());
        self::assertEquals($video->getId(), $queue->getVideoId());
        self::assertEquals($format->getId(), $queue->getFormatId());
        self::assertEquals($queue->created_at, $queue->getCreatedAt());
        self::assertEquals($queue->updated_at, $queue->getUpdatedAt());
    }

    public function test_relations(): void
    {
        Event::fake();

        $video = Video::factory()->create();
        $format = VideoFormats::factory()->create(['video_id' => $video->getId()]);

        $queue = VideoDownloadQueue::create([
            'video_id' => $video->getId(),
            'format_id' => $format->getId(),
        ]);

        self::assertEquals($video->getId(), $queue->video->getId());
        self::assertEquals($format->getId(), $queue->format->getId());
    }

    public function test_video_status_model(): void
    {
        $status = VideoStatus::create([
            'title' => 'Test Video Status',
            'code' => 'test-video-status',
        ]);

        self::assertEquals('Test Video Status', $status->title);
        self::assertEquals('test-video-status', $status->code);
        self::assertArrayNotHasKey('created_at', $status->getAttributes());
        self::assertArrayNotHasKey('updated_at', $status->getAttributes());
    }
}
