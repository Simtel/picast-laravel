<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Domains;

use App\Context\Domains\Application\Contract\WhoisUpdater;
use App\Context\Domains\Domain\Event\DomainCreated;
use App\Context\Domains\Domain\Model\Domain;
use App\Context\Domains\Infrastructure\EventListener\GetWhoisDomain;
use Event;
use Mockery;
use Mockery\ExpectationInterface;
use Tests\TestCase;

class GetWhoisDomainListenerTest extends TestCase
{
    public function test_created_domain_event_listener(): void
    {
        Event::fake();

        $domain = Domain::factory()->create();

        /** @var ExpectationInterface $mockWhoisUpdater */
        $mockWhoisUpdater = Mockery::mock(WhoisUpdater::class)
            ->expects('update')
            ->with($domain);
        /** @var WhoisUpdater $mockWhoisUpdater */
        $mockWhoisUpdater = $mockWhoisUpdater->getMock();

        $listener = new GetWhoisDomain($mockWhoisUpdater);

        $listener->handle(new DomainCreated($domain));


    }
}
