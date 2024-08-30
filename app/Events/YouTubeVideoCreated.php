<?php

namespace App\Events;

use App\Models\YouTubeVideo;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
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

    public function broadcastOn(): Channel|PrivateChannel|array
    {
        return new PrivateChannel('channel-name');
    }
}
