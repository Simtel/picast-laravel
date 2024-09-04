<?php

namespace App\Events;

use App\Models\Youtube\YouTubeVideo;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class YouTubeVideoCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public YouTubeVideo $video;


    public function __construct(YouTubeVideo $video)
    {

        $this->video = $video;
    }
}
