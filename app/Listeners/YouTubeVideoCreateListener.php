<?php

namespace App\Listeners;

use Alaouy\Youtube\Facades\Youtube;
use App\Events\YouTubeVideoCreated;
use Exception;

class YouTubeVideoCreateListener
{
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
    }
}
