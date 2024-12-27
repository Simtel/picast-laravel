<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Domains;

use App\Context\Domains\Domain\Model\Domain;
use App\Context\Domains\Domain\Model\Whois;
use App\Context\Domains\Infrastructure\Notification\DomainDeleted;
use App\Context\User\Domain\Model\User;
use Tests\TestCase;

class DomainTest extends TestCase
{
    public function test_domain_has_user(): void
    {
        \Event::fake();
        $user = User::factory()->create();
        $domain = Domain::factory()->create(['user_id' => $user->getId()]);

        self::assertEquals($user->getId(), $domain->user->getId());
    }

    public function test_domain_has_whois(): void
    {
        \Event::fake();
        $domain = Domain::factory()->create();
        $whois1 = Whois::factory()->create(['domain_id' => $domain->getId()]);
        $whois2 = Whois::factory()->create(['domain_id' => $domain->getId()]);

        self::assertEquals(
            [$whois1->id, $whois2->id],
            $domain->whois->pluck('id')->toArray()
        );
    }

    public function test_domain_has_notification_route(): void
    {
        $domain = Domain::factory()->make();
        $event = new DomainDeleted($domain);
        self::assertEquals(
            [
                config('DEFAULT_USER_EMAIL') => 'Admin'
            ],
            $domain->routeNotificationForMail($event),
        );
    }
}
