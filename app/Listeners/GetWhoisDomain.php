<?php

namespace App\Listeners;

use App\Events\DomainCreated;
use App\Facades\Whois;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GetWhoisDomain
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DomainCreated  $event
     * @return void
     */
    public function handle(DomainCreated $event)
    {
        $whois = Whois::loadDomainInfo($event->domain->name);
        \App\Models\Whois::create(
            [
                'domain_id' => $event->domain->id,
                'text' => $whois->getResponse()->text,
            ]
        );
        $event->domain->expire_at = Carbon::createFromTimestamp($whois->getExpirationDate());
        $event->domain->owner = $whois->getOwner();
        $event->domain->save();
    }
}
