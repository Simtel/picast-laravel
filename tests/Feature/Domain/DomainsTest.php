<?php

declare(strict_types=1);

namespace Tests\Feature\Domain;

use App\Context\Domains\Domain\Event\DomainCreated;
use App\Context\Domains\Domain\Model\Domain;
use App\Context\Domains\Infrastructure\Job\CheckExpireDomains;
use App\Context\Domains\Infrastructure\Mail\ExpireDomainNotify;
use App\Context\Domains\Infrastructure\Notification\DomainDeleted;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DomainsTest extends TestCase
{
    /**
     * Проверка что созадается задача если подходит срок истекания домена
     *
     * @return void
     */
    public function test_create_job_domain_expire_notify(): void
    {
        Event::fake();
        Mail::fake();
        $user = $this->getAdminUser();
        $domain = new Domain();
        $domain->id = 1;
        $domain->name = 'prosf.ru';
        $domain->user_id = $user->getId();
        $domain->expire_at = Carbon::now()->addDays(8);
        $domain->save();

        $checkJob = $this->partialMock(CheckExpireDomains::class);

        $checkJob->handle();

        Mail::assertSent(ExpireDomainNotify::class, static function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

    }

    /**
     * @throws Exception
     */
    public function test_domain_created(): void
    {
        Notification::fake();

        Event::fake([DomainCreated::class, DomainDeleted::class]);

        $domain = Domain::factory(1)->create();
        $domain->first()?->delete();

        Event::assertDispatched(DomainCreated::class, 1);
    }

}
