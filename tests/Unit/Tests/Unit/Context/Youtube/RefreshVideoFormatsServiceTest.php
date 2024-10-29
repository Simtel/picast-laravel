<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Youtube;

use Alaouy\Youtube\Facades\Youtube;
use App\Context\Youtube\Application\Service\GetVideoFormatsService;
use App\Context\Youtube\Application\Service\RefreshVideoFormatsService;
use App\Context\Youtube\Domain\Dto\FormatVideoDto;
use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Domain\Model\VideoFormats;
use App\Context\Youtube\Infrastructure\Repository\YouTubeVideoStatusRepository;
use Event;
use Mockery;
use stdClass;
use Tests\TestCase;

class RefreshVideoFormatsServiceTest extends TestCase
{
    public function test_refresh_video_formats(): void
    {
        Event::fake();
        $repository = $this->app->make(YouTubeVideoStatusRepository::class);

        /** @var GetVideoFormatsService $getVideoFormatsService */
        $getVideoFormatsService = Mockery::mock(GetVideoFormatsService::class)
            ->expects('getVideoFormats')
            ->andReturn([
                new FormatVideoDto(
                    132,
                    'some note for video',
                    'mp4',
                    'libx264',
                    '1280x720',
                )
            ])->getMock();

        $video = Video::factory()->create(['url' => 'https://www.youtube.com/watch?v=BRCsU4D852M']);
        $format = VideoFormats::factory()->create(['video_id' => $video->id]);

        $this->assertDatabaseCount(VideoFormats::class, 1);
        $this->assertDatabaseHas(VideoFormats::class, ['format_id' => $format->format_id,'video_id' => $video->getId()]);

        $service = new RefreshVideoFormatsService($getVideoFormatsService, $repository);



        $this->mockParseVidFromUrl($video->getUrl(), 'BRCsU4D852M');
        $this->mockGetVideoInfo('BRCsU4D852M');


        $service->refresh($video);

        $this->assertDatabaseCount(VideoFormats::class, 1);
        $this->assertDatabaseHas(VideoFormats::class, ['format_id' => 132, 'video_id' => $video->getId()]);
    }

    private function mockGetVideoInfo(string $videoId, string $title = 'Тестовый заголовок'): void
    {
        $stdClass = new StdClass();
        $stdClass->snippet = new StdClass();
        $stdClass->snippet->title = $title;

        Youtube::shouldReceive('getVideoInfo')
            ->once()
            ->with($videoId)
            ->andReturn($stdClass);
    }

    private function mockParseVidFromUrl(string $url, string $videoId): void
    {
        Youtube::shouldReceive('parseVidFromURL')
            ->once()
            ->with($url)
            ->andReturn($videoId);
    }
}
