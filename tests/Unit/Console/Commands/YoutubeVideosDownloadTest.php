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
        $video->shouldReceive('whereStatusId')->andReturnSelf();
        $video->shouldReceive('get')->andReturn(collect());

        // Запускаем команду
        $this->artisan('youtube:download')
            ->assertExitCode(0);
    }

    public function test_handle_video_without_url(): void
    {
        $videoMock = Mockery::mock(Video::class);
        $videoMock->url = '';
        $videoMock->allows('save')->never();


        $videoMock->shouldReceive('whereStatusId')->andReturnSelf();
        $videoMock->shouldReceive('get')->andReturn(collect([$videoMock]));

        // Запускаем команду
        $this->artisan('youtube:download')
            ->expectsOutput('Закончили скачивание')
            ->assertExitCode(0);
    }

    public function test_handle_successful_download(): void
    {
        // Настраиваем видео
        $videoMock = Mockery::mock(Video::class);
        $videoMock->url = 'https://www.youtube.com/watch?v=videoId123';
        $videoMock->id = 1;
        $videoMock->title = 'Test Video';

        // Замокаем формат видео
        $formatMock = Mockery::mock(VideoFormats::class);
        $formatMock->resolution = '1920x1080';
        $formatMock->format_ext = 'mp4';
        $formatMock->id = 1;
        $formatMock->format_id = 'best';

        $videoMock->shouldReceive('getAttribute')->with('formats')->andReturn(collect([$formatMock]));
        $videoMock->shouldReceive('save')->once();

        Video::shouldReceive('whereStatusId')->andReturnSelf();
        Video::shouldReceive('get')->andReturn(collect([$videoMock]));

        // Настраиваем YoutubeDl
        // Здесь можно замокать YoutubeDl и его методы

        // Запускаем команду
        $this->artisan('youtube:download')
            ->expectsOutput('Загрузка видео...')
            ->expectsOutput('Обработка видео:https://www.youtube.com/watch?v=videoId123')
            ->expectsOutput('Test Video')
            // Добавить дополнительные ожидания по необходимости
            ->assertExitCode(0);
    }

    // Дополнительные тесты для других сценариев...
}

