<?php

namespace App\Context\Youtube\Domain\Observer;

use App\Context\Youtube\Domain\Event\YouTubeVideoCreated;
use App\Context\Youtube\Domain\Model\Video;

class YouTubeVideoObserver
{
    public function created(Video $video): void
    {
        event(new YouTubeVideoCreated($video));
    }
}
