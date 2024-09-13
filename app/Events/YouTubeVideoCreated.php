<?php

namespace App\Events;

use App\Models\Youtube\Video;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class YouTubeVideoCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Video $video;


    public function __construct(Video $video)
    {

        $this->video = $video;
    }
}
