<?php

namespace App\Observers;

use App\Events\DomainCreated;
use App\Models\Domain;
use App\Notifications\DomainDeleted;

class DomainObserver
{
    public function __construct()
    {
    }

    /**
     * Handle the Domain "created" event.
     *
     * @param Domain $domain
     * @return void
     */
    public function created(Domain $domain): void
    {
        event(new DomainCreated($domain));
    }

    /**
     * Handle the Domain "updated" event.
     *
     * @param Domain $domain
     * @return void
     */
    public function updated(Domain $domain): void
    {
        //
    }

    /**
     * Handle the Domain "deleted" event.
     *
     * @param Domain $domain
     * @return void
     */
    public function deleted(Domain $domain): void
    {
        $domain->notify(new DomainDeleted($domain));
    }

    /**
     * Handle the Domain "restored" event.
     *
     * @param Domain $domain
     * @return void
     */
    public function restored(Domain $domain): void
    {
        //
    }

    /**
     * Handle the Domain "force deleted" event.
     *
     * @param Domain $domain
     * @return void
     */
    public function forceDeleted(Domain $domain): void
    {
        //
    }
}
