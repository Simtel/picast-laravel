<?php

declare(strict_types=1);

namespace Tests\Feature\Domain;

use App\Context\Domains\Application\Contract\WhoisUpdater;
use App\Context\Domains\Domain\Event\DomainCreated;
use App\Context\Domains\Domain\Model\Domain;
use App\Context\Domains\Domain\Model\Whois;
use App\Context\Domains\Infrastructure\Notification\DomainDeleted;
use App\Services\Notifications\TelegramChannelNotification;
use Illuminate\Support\Facades\Event;
use Mockery;
use Mockery\ExpectationInterface;
use Notification;
use Tests\TestCase;

class DomainControllerTest extends TestCase
{
    /**
     * @dataProvider dataProviderForValidationTest
     * @param string|int $name
     * @param string $error
     * @return void
     */
    public function test_validate_domain_name(string|int $name, string $error): void
    {
        $this->loginAdmin();

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

    public function test_user_can_create_a_domain(): void
    {
        $this->loginAdmin();

        Event::fake([DomainCreated::class]);

        $data = [
            'name' => 'armisimtel.ru',
        ];

        $response = $this->post(route('domains.store'), $data);
        $response->assertStatus(302);
        Event::assertDispatched(DomainCreated::class, 1);
        $this->assertDatabaseCount(Domain::class, 1);
        $this->assertDatabaseHas(Domain::class, ['name' => 'armisimtel.ru']);
    }

    public function test_user_can_see_list_domains(): void
    {
        $this->loginAdmin();
        $user = $this->getAuthUser();
        if ($user === null) {
            self::fail('Auth user not found');
        }
        Event::fake([DomainCreated::class]);
        $domain1 = Domain::factory()->make(['user_id' => $user->getId()]);
        $domain2 = Domain::factory()->make(['user_id' => $user->getId()]);
        $domain1->save();
        $domain2->save();

        $response = $this->get(route('domains.index'));
        $response->assertStatus(200);
        $response->assertViewIs('personal.domains.index');
        $response->assertViewHas('domains');
        $response->assertSee($domain1->getName());
        $response->assertSee($domain2->getName());
    }

    public function test_user_can_delete_domain(): void
    {
        $this->loginAdmin();
        $user = $this->getAuthUser();
        if ($user === null) {
            self::fail('Auth user not found');
        }
        Notification::fake();
        Event::fake([DomainCreated::class, DomainDeleted::class]);
        $domain1 = Domain::factory()->make(['user_id' => $user->getId()]);
        $domain2 = Domain::factory()->make(['user_id' => $user->getId()]);
        $domain1->save();
        $domain2->save();
        Event::assertDispatched(DomainCreated::class, 2);

        $response = $this->delete(route('domains.destroy', $domain1));
        $response->assertStatus(302);
        $this->assertDatabaseCount(Domain::class, 1);
        $this->assertDatabaseHas(Domain::class, ['name' => $domain2->getName()]);
    }

    public function test_user_can_see_add_form(): void
    {
        $this->loginAdmin();

        $response = $this->get(route('domains.create'));

        $response->assertStatus(200);
        $response->assertSee('Сохранить');
        $response->assertSee('Домен');
    }

    public function test_user_can_see_domain_info(): void
    {
        $this->loginAdmin();
        $user = $this->getAuthUser();
        if ($user === null) {
            self::fail('Auth user not found');
        }

        Event::fake();
        $domain = Domain::factory()->make(['user_id' => $user->getId()]);
        $domain->save();
        $whois = Whois::factory()->make(['domain_id' => $domain->getId()]);
        $whois->save();

        $response = $this->get(route('domains.show', $domain->getId()));
        $response->assertStatus(200);
        $response->assertViewIs('personal.domains.show');
        $response->assertViewHas('domain', $domain);
        $response->assertViewHas('whois', Whois::whereDomainId($domain->id)->paginate(15));
    }

    public function test_user_can_edit_domain(): void
    {
        $this->loginAdmin();
        $user = $this->getAuthUser();
        if ($user === null) {
            self::fail('Auth user not found');
        }

        Event::fake();
        $domain = Domain::factory()->make(['user_id' => $user->getId()]);
        $domain->save();
        $response = $this->get(route('domains.edit', $domain->getId()));
        $response->assertStatus(403);
    }

    public function test_user_can_update_domain(): void
    {
        $this->loginAdmin();
        $user = $this->getAuthUser();
        if ($user === null) {
            self::fail('Auth user not found');
        }

        Event::fake();
        $domain = Domain::factory()->make(['user_id' => $user->getId()]);
        $domain->save();

        Notification::fake();

        /** @var ExpectationInterface $mockWhoisUpdater */
        $mockWhoisUpdater = Mockery::mock(WhoisUpdater::class)
            ->expects('update');


        /** @var ExpectationInterface $mockNotification */
        $mockNotification = Mockery::mock(TelegramChannelNotification::class)
            ->expects('sendTextToChannel');

        $this->app->instance(WhoisUpdater::class, $mockWhoisUpdater->getMock());
        $this->app->instance(TelegramChannelNotification::class, $mockNotification->getMock());

        $response = $this->put(route('domains.update', $domain->getId()));
        $response->assertStatus(302);
        $response->assertRedirect(route('domains.index'));
    }
}