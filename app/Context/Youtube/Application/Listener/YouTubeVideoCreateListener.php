<?php

namespace App\Context\Youtube\Application\Listener;

use Alaouy\Youtube\Facades\Youtube;
use App\Context\Youtube\Domain\Event\YouTubeVideoCreated;
use App\Context\Youtube\Infrastructure\Jobs\UpdateVideoFormats;
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
