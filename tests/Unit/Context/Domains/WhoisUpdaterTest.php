<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Domains;

use App\Context\Domains\Application\Contract\WhoisUpdater;
use App\Context\Domains\Domain\Model\Domain;
use App\Context\Domains\Domain\Model\Whois as WhoisModel;
use App\Context\Domains\Infrastructure\Facades\Whois;
use Event;
use Iodev\Whois\Modules\Tld\TldInfo;
use Iodev\Whois\Modules\Tld\TldResponse;
use Tests\TestCase;

class WhoisUpdaterTest extends TestCase
{
    public function test_whois_update(): void
    {
        Event::fake();
        $domain = Domain::factory()->create();

        $this->assertDatabaseCount(Domain::class, 1);
        $this->assertDatabaseCount(WhoisModel::class, 0);

        $info = self::createInfo($domain);
        $info->expirationDate = now()->add('100 days')->getTimestamp();
        $info->owner = 'Simtel';

        Whois::expects('loadDomainInfo')
            ->with($domain->name)
            ->andReturn($info);

        $updater = $this->app->make(WhoisUpdater::class);

        $updater->update($domain);

        $this->assertDatabaseCount(WhoisModel::class, 1);
        $this->assertDatabaseHas(WhoisModel::class, ['domain_id' => $domain->id]);
    }

    private static function createInfo(Domain $domain): TldInfo
    {
        return new TldInfo(self::getResponse($domain));
    }

    private static function getResponse(Domain $domain): TldResponse
    {
        return new TldResponse([
            "domain" => $domain->getName(),
            "query" => $domain->getName(),
            "text" => "Hello world",
        ]);
    }
}
