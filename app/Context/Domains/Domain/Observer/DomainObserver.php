<?php

declare(strict_types=1);

namespace App\Context\Domains\Domain\Observer;

use App\Context\Domains\Domain\Event\DomainCreated;
use App\Context\Domains\Domain\Model\Domain;
use App\Context\Domains\Infrastructure\Notification\DomainDeleted;

class DomainObserver
{
    public function __construct()
    {
    }

    /**
     * Handle the Domain "created" event.
     */
    public function created(Domain $domain): void
    {
        event(new DomainCreated($domain));
    }

    /**
     * Handle the Domain "updated" event.
     */
    public function updated(Domain $domain): void
    {
        //
    }

    /**
     * Handle the Domain "deleted" event.
     */
    public function deleted(Domain $domain): void
    {
        $domain->notify(new DomainDeleted($domain));
    }

    /**
     * Handle the Domain "restored" event.
     */
    public function restored(Domain $domain): void
    {
        //
    }

    /**
     * Handle the Domain "force deleted" event.
     */
    public function forceDeleted(Domain $domain): void
    {
        //
    }
}
