<?php

namespace Tests\Feature;

use App\Events\DomainCreated;
use App\Jobs\CheckExpireDomains;
use App\Jobs\SendDomainExpireNotify;
use App\Models\Domain;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
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
        $this->expectsJobs(SendDomainExpireNotify::class);
        $domain = $this->partialMock(Domain::class);
        $domain->id = 1;
        $domain->name = 'prosf.ru';
        $domain->user_id = 1;
        $domain->expire_at = Carbon::now()->addDays(7);
        $domain->user = User::find(1);
        $domains = Collection::make([$domain]);

        $checkJob = $this->partialMock(CheckExpireDomains::class);
        $checkJob->shouldAllowMockingProtectedMethods();
        $checkJob->shouldReceive('getDomains')->andReturn($domains);
        $checkJob->handle();
    }

    /**
     * @throws Exception
     */
    public function test_domain_created(): void
    {
        $this->expectsEvents(DomainCreated::class);
        $domain = Domain::factory(1)->create();
        $domain->first()->delete();
    }
}
