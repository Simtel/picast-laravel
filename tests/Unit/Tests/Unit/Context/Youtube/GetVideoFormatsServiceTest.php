<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Youtube;

use App\Context\Youtube\Application\Service\GetVideoFormatsService;
use App\Context\Youtube\Domain\Dto\FormatVideoDto;
use App\Context\Youtube\Domain\Model\Video;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class GetVideoFormatsServiceTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test_get_video_formats(): void
    {

        $video = Video::factory()->make();

        Process::fake([
           "youtube-dl --dump-json 'https://www.youtube.com/watch?v=BRCsU4D852M'" => Process::result(
               output: '{"formats":[{"height":720,"format_id":"12","video_ext":"mp4","vcodec":"libx264","resolution":"1280x720","format_note":"some note for video"}]}',
           ),
        ]);

        $service = $this->app->make(GetVideoFormatsService::class);


        $expected = [
            new FormatVideoDto(
                12,
                'some note for video',
                'mp4',
                'libx264',
                '1280x720',
            )
        ];
        self::assertEquals($expected, $service->getVideoFormats($video));
    }

    /**
     * @throws \Exception
     */
    public function test_invalid_video_formats(): void
    {

        $video = Video::factory()->make();

        Process::fake([
            "youtube-dl --dump-json 'https://www.youtube.com/watch?v=BRCsU4D852M'" => Process::result(
                output: '{"video_formats":[{"height":720,"format_id":"12","video_ext":"mp4","vcodec":"libx264","resolution":"1280x720","format_note":"some note for video"}]}',
            ),
        ]);

        $service = $this->app->make(GetVideoFormatsService::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Не удалось получить информацию о видео');

        $service->getVideoFormats($video);

    }
}
