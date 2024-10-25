<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Domains;

use App\Context\Domains\Domain\Model\Domain;
use App\Context\Domains\Infrastructure\Notification\DomainDeleted;
use Event;
use Tests\TestCase;

class DomainDeletedNotifyTest extends TestCase
{
    public function test_domain_deleted_notify(): void
    {
        Event::fake();
        $domain = Domain::factory()->create();

        $notify = new DomainDeleted($domain);
        self::assertEquals([], $notify->toArray(new \stdClass()));
        $mail = $notify->toMail(new \stdClass());
        self::assertContains($domain->getName() . ' был удален из системы.', $mail->introLines);
        $telegram = $notify->toTelegram(new \stdClass());
        self::assertEquals('Домен ' . $domain->getName() . ' был удален из системы.', $telegram->getMessage());
    }
}
