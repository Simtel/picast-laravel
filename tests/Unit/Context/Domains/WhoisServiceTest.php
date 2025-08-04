<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Domains;

use App\Context\Domains\Application\Contract\WhoisService;
use App\Context\Domains\Domain\Factory\DomainFactory;
use App\Context\Domains\Domain\Model\Domain;
use App\Context\Domains\Domain\Model\Whois;
use Event;
use Illuminate\Contracts\Container\BindingResolutionException;
use Queue;
use Tests\TestCase;

class WhoisServiceTest extends TestCase
{
    /**
     * @throws BindingResolutionException
     */
    public function test_time_frame_options(): void
    {
        $service = $this->app->make(WhoisService::class);

        $this->assertEquals(
            [
                'day' => '1 дня',
                'week' => '1 недели',
                'month' => '1 месяца',
            ],
            $service->getTimeFrameOptions(),
        );
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_delete_old_whois(): void
    {
        Queue::fake();
        Event::fake();
        $domain = Domain::factory()->create();
        Whois::factory()->make(['domain_id' => $domain->getId()])->save();
        Whois::factory()->make(['domain_id' => $domain->getId(), 'created_at' => now()->sub('2 day')])->save();
        Whois::factory()->create();


        $this->assertDatabaseCount(Domain::class, 2);
        $this->assertDatabaseCount(Whois::class, 3);
        $service = $this->app->make(WhoisService::class);

        self::assertEquals(1, $service->deleteOldWhois($domain, 'day'));

        $this->assertDatabaseCount(Domain::class, 2);
        $this->assertDatabaseCount(Whois::class, 2);
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_nothing_delete_old_whois(): void
    {
        Queue::fake();
        Event::fake();
        $domain = Domain::factory()->create();
        Whois::factory()->make(['domain_id' => $domain->getId()])->save();
        Whois::factory()->make()->save();


        $this->assertDatabaseCount(Domain::class, 2);
        $this->assertDatabaseCount(Whois::class, 2);
        $service = $this->app->make(WhoisService::class);

        self::assertEquals(0, $service->deleteOldWhois($domain, 'day'));

        $this->assertDatabaseCount(Domain::class, 2);
        $this->assertDatabaseCount(Whois::class, 2);
    }
}
