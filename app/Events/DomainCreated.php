<?php

namespace App\Events;

use App\Models\Domain;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DomainCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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
