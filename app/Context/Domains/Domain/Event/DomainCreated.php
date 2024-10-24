<?php

declare(strict_types=1);

namespace App\Context\Domains\Domain\Event;

use App\Context\Domains\Domain\Model\Domain;
use Illuminate\Broadcasting\InteractsWithSockets;
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
        $this->domain = $domain;
    }

}
