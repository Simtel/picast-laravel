<?php

namespace App\Listeners;

use Alaouy\Youtube\Facades\Youtube;
use App\Events\YouTubeVideoCreated;
use App\Jobs\UpdateVideoFormats;
use Exception;

readonly class YouTubeVideoCreateListener
{
    public function __construct(

    ) {
    }

    /**
     * Handle the event.
     * @throws Exception
     */
    public function handle(YouTubeVideoCreated $event): void
    {
        $video = $event->video;
        $videoInfo = Youtube::getVideoInfo($video->getVideoId());
        $video->title = $videoInfo->snippet->title;
        $video->save();

        UpdateVideoFormats::dispatch($video);
    }
}
