<?php

namespace App\Observers;

use App\Events\YouTubeVideoCreated;
use App\Exceptions\NotFoundS3VideoFileException;
use App\Models\Youtube\YouTubeVideo;

class YouTubeVideoObserver
{
    public function created(YouTubeVideo $video): void
    {
        event(new YouTubeVideoCreated($video));
    }

    /**
     * @param YouTubeVideo $video
     * @throws NotFoundS3VideoFileException
     */
    public function deleting(YouTubeVideo $video): void
    {
        $video->deleteFile();
    }
}
