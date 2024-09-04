<?php

namespace App\Listeners;

use Alaouy\Youtube\Facades\Youtube;
use App\Events\YouTubeVideoCreated;
use App\Models\Youtube\VideoFormats;
use App\Services\Youtube\GetVideoFormatsService;
use Exception;

readonly class YouTubeVideoCreateListener
{
    public function __construct(
        private GetVideoFormatsService $getVideoFormatsService,
    ) {
    }

    /**
     * Handle the event.
     * @throws Exception
     */
    public function handle(YouTubeVideoCreated $event): void
    {
        $video = $event->video;
        $videoId = Youtube::parseVidFromURL($video->url);
        $videoInfo = Youtube::getVideoInfo($videoId);
        $video->title = $videoInfo->snippet->title;
        $video->save();
        $formats = $this->getVideoFormatsService->getVideoFormats($videoId);
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
