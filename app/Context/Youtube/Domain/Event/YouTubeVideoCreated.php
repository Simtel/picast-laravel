<?php

declare(strict_types=1);

namespace App\Context\Youtube\Domain\Event;

use App\Context\Youtube\Domain\Model\Video;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class YouTubeVideoCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;


    public function __construct(public Video $video)
    {
    }
}
