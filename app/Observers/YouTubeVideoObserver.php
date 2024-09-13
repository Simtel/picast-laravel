<?php

namespace App\Observers;

use App\Events\YouTubeVideoCreated;
use App\Models\Youtube\Video;

class YouTubeVideoObserver
{
    public function created(Video $video): void
    {
        event(new YouTubeVideoCreated($video));
    }
}
