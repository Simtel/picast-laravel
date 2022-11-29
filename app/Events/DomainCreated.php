<?php

namespace App\Events;

use App\Models\Domain;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DomainCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Domain $domain;


    /**
     * Create a new event instance.
     *
     * @param Domain $domain
     */
    public function __construct(Domain $domain)
    {
        //
        $this->domain = $domain;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
