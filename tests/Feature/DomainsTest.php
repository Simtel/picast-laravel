<?php

namespace Tests\Feature;

use App\Events\DomainCreated;
use App\Jobs\CheckExpireDomains;
use App\Jobs\SendDomainExpireNotify;
use App\Models\Domain;
use App\Models\User;
use App\Notifications\DomainDeleted;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
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
        Queue::fake();

        $domain = $this->partialMock(Domain::class);
        $domain->id = 1;
        $domain->name = 'prosf.ru';
        $domain->user_id = 1;
        $domain->expire_at = Carbon::now()->addDays(8);
        $domain->user = User::find(1);
        $domains = Collection::make([$domain]);

        $checkJob = $this->partialMock(CheckExpireDomains::class);
        $checkJob->shouldAllowMockingProtectedMethods();
        $checkJob->shouldReceive('getDomains')->andReturn($domains);
        $checkJob->handle();

        Queue::assertPushed(SendDomainExpireNotify::class, 1);
    }

    /**
     * @throws Exception
     */
    public function test_domain_created(): void
    {
        Notification::fake();

        Event::fake([DomainCreated::class, DomainDeleted::class]);

        $domain = Domain::factory(1)->create();
        $domain->first()->delete();

        Event::assertDispatched(DomainCreated::class, 1);
    }
}
