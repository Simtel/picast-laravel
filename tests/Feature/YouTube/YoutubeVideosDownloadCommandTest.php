<?php

declare(strict_types=1);

namespace Tests\Feature\YouTube;

use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Domain\Model\VideoFormats;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\PendingCommand;
use Tests\TestCase;
use YoutubeDl\Entity\VideoCollection;
use YoutubeDl\YoutubeDl;

class YoutubeVideosDownloadCommandTest extends TestCase
{
    public function test_youtube_video_command(): void
    {
        $this->beginDatabaseTransaction();
        Event::fake();
        Storage::fake();

        $video = Video::factory()->create();
        VideoFormats::factory()->create(['video_id' => $video->getId(), 'resolution' => '1920x1080']);


        $element = $this->getMockBuilder(\YoutubeDl\Entity\Video::class)
            ->disableOriginalConstructor()
            ->getMock();
        $element->expects($this->once())->method('getError')->willReturn(null);
        $element->expects($this->exactly(2))->method('getExt')->willReturn('mp4');
        $filePath = 'public/videos' . '/' . $video->getVideoId() . '.mp4';
        Storage::shouldReceive('disk')->with('local')->andReturnSelf();
        Storage::shouldReceive('disk')->with('s3')->andReturnSelf();
        Storage::shouldReceive('exists')->with($filePath)->andReturn(true);
        Storage::shouldReceive('readStream')->with($filePath)->andReturn('video content');
        Storage::shouldReceive('exists')->with('videos/' . $video->getVideoId() . '.mp4')->andReturn(true);
        Storage::shouldReceive('put')->with('videos/' . $video->getVideoId() . '.mp4', 'video content');
        Storage::shouldReceive('path')->with('public/videos')->andReturn($filePath);
        Storage::shouldReceive('size')->with($filePath)->andReturn(123);
        Storage::shouldReceive('delete')->with($filePath);


        $youtubeDl = $this->getMockBuilder(YoutubeDl::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['onProgress', 'download'])
            ->getMock();
        $youtubeDl->expects($this->once())->method('onProgress')->willReturnSelf();
        $youtubeDl->expects($this->once())->method('download')->with()->willReturn(new VideoCollection([$element]));

        $this->instance(YoutubeDl::class, $youtubeDl);


        /** @var  PendingCommand $command */
        $command = $this->artisan('youtube:download');
        $command->assertSuccessful();
        $command->expectsOutput('В очереди на загрузку: 1');
        $command->expectsOutput('Обработка видео:' . $video->getUrl());
        $command->expectsOutput($filePath);
        $command->expectsOutput('Закончили скачивание');

    }
}