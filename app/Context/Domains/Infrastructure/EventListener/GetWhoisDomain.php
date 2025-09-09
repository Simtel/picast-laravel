<?php

declare(strict_types=1);

namespace App\Context\Domains\Infrastructure\EventListener;

use App\Context\Domains\Application\Contract\WhoisUpdater;
use App\Context\Domains\Domain\Event\DomainCreated;

final class GetWhoisDomain
{
    private WhoisUpdater $whoisUpdater;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(WhoisUpdater $whoisUpdater)
    {
        $this->whoisUpdater = $whoisUpdater;
    }

    /**
     * Handle the event.
     *
     * @param DomainCreated $event
     * @return void
     */
    public function handle(DomainCreated $event)
    {
        $this->whoisUpdater->update($event->domain);
    }
}
