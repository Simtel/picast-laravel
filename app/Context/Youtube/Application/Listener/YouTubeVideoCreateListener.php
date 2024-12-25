<?php

declare(strict_types=1);

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
        $title = $videoInfo->snippet->title;
        if (!is_string($title)) {
            $title = strval($title);
        }
        $video->title = $title;
        $video->save();

        UpdateVideoFormats::dispatch($video);
    }
}
