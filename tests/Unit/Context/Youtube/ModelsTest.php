<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Youtube;

use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Domain\Model\VideoFile;
use App\Context\Youtube\Domain\Model\VideoFormats;
use App\Context\Youtube\Domain\Model\VideoStatus;
use Event;
use Illuminate\Support\Number;
use Storage;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    public function test_video_delete_with_files(): void
    {
        Event::fake();
        Storage::fake('s3');
        $video = Video::factory()->create();
        $format = VideoFormats::factory()->create(['video_id' => $video->getId()]);
        $file = VideoFile::factory()->create(['video_id' => $video->getId(), 'format_id' => $format->getId()]);
        $this->assertDatabaseCount(VideoFile::class, 1);
        $this->assertDatabaseCount(Video::class, 1);

        $video = $file->video;
        $video->delete();
        $this->assertDatabaseCount(VideoFile::class, 0);
        $this->assertDatabaseCount(Video::class, 0);
    }

    public function test_set_video_download(): void
    {
        Event::fake();
        $statusId = VideoStatus::whereCode('downloaded')->first()?->id;
        if ($statusId === null) {
            self::fail('Not found downloaded status');
        }
        $video = Video::factory()->create();
        $video->setDownloadedStatus();
        $this->assertDatabaseCount(Video::class, 1);
        self::assertEquals($video->status->id, $statusId);
    }

    public function test_delete_video_formats_with_files(): void
    {
        Event::fake();
        Storage::fake('s3');
        $video = Video::factory()->create();
        $format = VideoFormats::factory()->create(['video_id' => $video->getId()]);
        $file = VideoFile::factory()->create(['video_id' => $video->getId(), 'format_id' => $format->getId()]);
        Storage::shouldReceive('disk')->with('s3')->twice()->andReturnSelf();
        Storage::shouldReceive('exists')->with('videos/' . $file->file_link)->andReturn(true);
        Storage::shouldReceive('delete')->with('videos/' . $file->file_link)->andReturn(true);
        $this->assertDatabaseCount(VideoFile::class, 1);
        $this->assertDatabaseCount(Video::class, 1);
        $this->assertDatabaseCount(VideoFormats::class, 1);

        $format->delete();
        $this->assertDatabaseCount(VideoFile::class, 0);
        $this->assertDatabaseCount(Video::class, 1);
        $this->assertDatabaseCount(VideoFormats::class, 0);

    }

    public function test_get_file_url(): void
    {
        Event::fake();
        Storage::fake('s3');
        $video = Video::factory()->create();
        $format = VideoFormats::factory()->create(['video_id' => $video->getId()]);
        $file = VideoFile::factory()->create(['video_id' => $video->getId(), 'format_id' => $format->getId()]);

        self::assertEquals('http://localhost/videos/'.$file->file_link, $file->getFileUrl());

        $file->file_link = '';
        self::assertEquals('', $file->getFileUrl());

    }

    public function test_get_file_size(): void
    {
        Event::fake();
        Storage::fake('s3');

        $file = VideoFile::factory()->create();

        self::assertEquals(Number::fileSize($file->size, 2), $file->getSize());

        $file->size = '';
        self::assertEquals('', $file->getSize());
    }
}
