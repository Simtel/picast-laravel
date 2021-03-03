<?php

namespace App\Observers;

use App\Events\DomainCreated;
use App\Models\Domain;

class DomainObserver
{
    /**
     * Handle the Domain "created" event.
     *
     * @param  \App\Models\Domain  $domain
     * @return void
     */
    public function created(Domain $domain)
    {
        event(new DomainCreated($domain));
    }

    /**
     * Handle the Domain "updated" event.
     *
     * @param  \App\Models\Domain  $domain
     * @return void
     */
    public function updated(Domain $domain)
    {
        //
    }

    /**
     * Handle the Domain "deleted" event.
     *
     * @param  \App\Models\Domain  $domain
     * @return void
     */
    public function deleted(Domain $domain)
    {
        //
    }

    /**
     * Handle the Domain "restored" event.
     *
     * @param  \App\Models\Domain  $domain
     * @return void
     */
    public function restored(Domain $domain)
    {
        //
    }

    /**
     * Handle the Domain "force deleted" event.
     *
     * @param  \App\Models\Domain  $domain
     * @return void
     */
    public function forceDeleted(Domain $domain)
    {
        //
    }
}
