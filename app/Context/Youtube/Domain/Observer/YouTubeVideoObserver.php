<?php

declare(strict_types=1);

namespace App\Context\Youtube\Domain\Observer;

use App\Context\Youtube\Domain\Event\YouTubeVideoCreated;
use App\Context\Youtube\Domain\Model\Video;

final class YouTubeVideoObserver
{
    public function created(Video $video): void
    {
        event(new YouTubeVideoCreated($video));
    }
}
