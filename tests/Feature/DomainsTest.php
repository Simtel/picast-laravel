<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Context\Domains\Domain\Event\DomainCreated;
use App\Context\Domains\Domain\Model\Domain;
use App\Context\Domains\Infrastructure\Job\CheckExpireDomains;
use App\Context\Domains\Infrastructure\Job\SendDomainExpireNotify;
use App\Context\Domains\Infrastructure\Notification\DomainDeleted;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Mockery\ExpectationInterface;
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
        $user = User::find(1);
        if ($user === null) {
            self::fail('User not found');
        }
        $domain->user = $user;
        $domains = Collection::make([$domain]);

        $checkJob = $this->partialMock(CheckExpireDomains::class);
        $checkJob->shouldAllowMockingProtectedMethods();
        $job = $checkJob->shouldReceive('getDomains');
        if ($job instanceof ExpectationInterface) {
            $job->andReturn($domains);
        }
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
        $domain->first()?->delete();

        Event::assertDispatched(DomainCreated::class, 1);
    }


    /**
     * @dataProvider dataProviderForValidationTest
     * @param string|int $name
     * @param string $error
     * @return void
     */
    public function test_validate_domain_name(string|int $name, string $error): void
    {
        /** @var User $user */
        $user = User::find(1);
        Auth::login($user);

        $data = [
            'name' => $name,
        ];

        $response = $this->post(route('domains.store'), $data);
        $response->assertInvalid(['name' => $error]);
    }

    /**
     * @return array<int,array{name:int|string, error: string}>
     */
    public static function dataProviderForValidationTest(): array
    {
        return [
            [
                'name'  => 'prosf',
                'error' => 'Неправильное имя домена.'
            ],
            [
                'name'  => 1,
                'error' => 'Имя домена должно быть строкой.'
            ]
        ];
    }
}
