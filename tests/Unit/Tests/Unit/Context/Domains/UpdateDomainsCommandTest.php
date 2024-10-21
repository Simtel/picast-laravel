<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Domains;

use App\Context\Domains\Application\Contract\WhoisUpdater;
use App\Context\Domains\Domain\Model\Domain;
use Illuminate\Testing\PendingCommand;
use Mockery;
use Tests\TestCase;

class UpdateDomainsCommandTest extends TestCase
{
    public function testHandle(): void
    {
        \Event::fake();
        $domain1 = Domain::factory()->create();
        $domain2 = Domain::factory()->create();


        $whoisUpdaterMock = Mockery::mock(WhoisUpdater::class);

        $whoisUpdaterMock->expects('update')
            ->twice()
            ->withArgs(function (Domain $domain) use ($domain1, $domain2) {
                return in_array($domain->name, [$domain1->getName(), $domain2->getName()], true);
            });

        $this->app->instance(WhoisUpdater::class, $whoisUpdaterMock);
        /** @var PendingCommand $command */
        $command = $this->artisan('domains:whois');

        $command->assertSuccessful();
    }
}
