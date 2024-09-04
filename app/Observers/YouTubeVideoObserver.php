<?php

namespace App\Observers;

use App\Events\YouTubeVideoCreated;
use App\Models\Youtube\YouTubeVideo;

class YouTubeVideoObserver
{
    public function __construct()
    {
    }


    public function created(YouTubeVideo $video): void
    {
        event(new YouTubeVideoCreated($video));
    }
}
