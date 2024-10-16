<?php

declare(strict_types=1);

namespace App\Context\Youtube\Application\Service;

use Alaouy\Youtube\Facades\Youtube;
use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Domain\Model\VideoFormats;
use App\Context\Youtube\Infrastructure\Repository\YouTubeVideoStatusRepository;
use Exception;

class RefreshVideoFormatsService
{
    public function __construct(
        readonly private GetVideoFormatsService $getVideoFormatsService,
        readonly private YouTubeVideoStatusRepository $statusRepository,
    ) {
    }

    /**
     * @throws Exception
     */
    public function refresh(Video $video): void
    {
        $videoInfo = Youtube::getVideoInfo($video->getVideoId());
        $video->title = $videoInfo->snippet->title;
        $video->save();
        $formats = $this->getVideoFormatsService->getVideoFormats($video);
        if (count($formats) > 0) {
            foreach ($video->formats as $format) {
                $format->delete();
            }
        }
        $video->status_id = $this->statusRepository->findByCode('new')->id;
        $video->save();

        foreach ($formats as $formatDto) {
            $format = new VideoFormats();
            $format->video_id = $video->id;

            $format->format_id = $formatDto->getFormatId();
            $format->format_note = $formatDto->getFormatNote();
            $format->format_ext = $formatDto->getVideoExt();
            $format->vcodec = $formatDto->getVCodec();
            $format->resolution = $formatDto->getResolution();
            $format->save();
        }
    }
}
