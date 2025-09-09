<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Domains;

use App\Context\Domains\Domain\Model\Domain;
use App\Context\Domains\Infrastructure\Mail\ExpireDomainNotify;
use Tests\TestCase;

final class ExpireDomainNotifyTest extends TestCase
{
    public function test_expire_domain_notify_mailable_content(): void
    {
        \Event::fake();
        $date = now()->sub('2 day');
        $domain = Domain::factory()->create(['expire_at' => $date]);
        $user = $domain->getUser();
        $mailable = new ExpireDomainNotify($domain, $user);


        $mailable->assertHasSubject('Информация о вашем домене');
        $mailable->assertSeeInHtml($domain->getName());
        $mailable->assertSeeInHtml($user->getName());
        $mailable->assertSeeInHtml($date->toDateString());

    }
}
