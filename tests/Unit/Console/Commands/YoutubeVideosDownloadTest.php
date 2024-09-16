<?php

namespace Console\Commands;

use App\Models\Youtube\Video;
use App\Models\Youtube\VideoFormats;
use App\Models\Youtube\VideoStatus;
use App\Repositories\YouTubeVideoStatusRepository;
use Mockery;
use Storage;
use Tests\TestCase;
use Youtube;

class YoutubeVideosDownloadTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // Замокаем фасады
        Storage::fake('local');
        Storage::fake('s3');
        Youtube::shouldReceive('parseVidFromURL')->andReturn('videoId123');

        $newStatus = (new VideoStatus());
        $newStatus->id = 1;

        // Замокаем репозиторий статусов
        $statusRepositoryMock = Mockery::mock(YouTubeVideoStatusRepository::class);
        $statusRepositoryMock->allows('findByCode')->with('new')->andReturns($newStatus);
        $statusRepositoryMock->allows('findByCode')->with('downloaded')->andReturns((object)['id' => 2]);
        $this->app->instance(YouTubeVideoStatusRepository::class, $statusRepositoryMock);
    }

    public function test_handle_no_new_videos(): void
    {
        $video = Mockery::mock(Video::class);
        // Настроим мок для Video
        $video->allows('whereStatusId')->andReturnSelf();
        $video->allows('get')->andReturns(collect());

        // Запускаем команду
        $this->artisan('youtube:download')
            ->assertExitCode(0);
    }

    public function test_handle_video_without_url(): void
    {
        $videoMock = Mockery::mock(Video::class);
        $videoMock->allows('setAttribute');
        $videoMock->url = '';
        $videoMock->allows('save')->never();

        $videoMock->allows('whereStatusId')->andReturnSelf();
        $videoMock->allows('get')->andReturns(collect([$videoMock]));


        $this->artisan('youtube:download')
            ->assertExitCode(0);
    }

    public function test_handle_successful_download(): void
    {
        // Настраиваем видео
        $videoMock = Mockery::mock(Video::class);
        $videoMock->allows('setAttribute');
        $videoMock->url = 'https://www.youtube.com/watch?v=videoId123';
        $videoMock->id = 1;
        $videoMock->title = 'Test Video';

        // Замокаем формат видео
        $formatMock = Mockery::mock(VideoFormats::class);
        $formatMock->allows('setAttribute');
        $formatMock->resolution = '1920x1080';
        $formatMock->format_ext = 'mp4';
        $formatMock->id = 1;
        $formatMock->format_id = 'best';

        $videoMock->allows('getAttribute')->with('formats')->andReturns(collect([$formatMock]));

        $videoMock->allows('whereStatusId')->andReturnSelf();
        $videoMock->allows('get')->andReturns(collect([$videoMock]));

        $this->artisan('youtube:download')
            ->assertExitCode(0);
    }


}
