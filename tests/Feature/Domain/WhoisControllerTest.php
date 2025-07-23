<?php

declare(strict_types=1);

namespace Tests\Feature\Domain;

use App\Context\Domains\Domain\Model\Domain;
use App\Context\Domains\Domain\Model\Whois;
use Event;
use Tests\TestCase;

class WhoisControllerTest extends TestCase
{
    public function test_delete_old_whois(): void
    {
        $this->loginAdmin();
        $user = $this->getAuthUser();
        if ($user === null) {
            self::fail('Auth user not found');
        }
        Event::fake();
        $domain = Domain::factory()->create(['user_id' => $user->getId()]);
        $domain2 = Domain::factory()->create(['user_id' => $user->getId()]);
        Whois::factory()->make(['domain_id' => $domain->getId()])->save();
        Whois::factory()->make(['domain_id' => $domain2->getId()])->save();
        Whois::factory()->make(['domain_id' => $domain->getId(), 'created_at' => now()->sub('2 day')])->save();
        Whois::factory()->make(['domain_id' => $domain2->getId(), 'created_at' => now()->sub('2 day')])->save();

        $this->assertDatabaseCount(Domain::class, 2);
        $this->assertDatabaseCount(Whois::class, 4);

        $response = $this->post(route('domains.delete_old_whois', ['id' => $domain->getId()]), ['delete_old_whois' => 'day']);
        $response->assertStatus(302);
        $response->assertRedirect(route('domains.show', ['domain' => $domain->id]));


        $this->assertDatabaseCount(Domain::class, 2);
        $this->assertDatabaseCount(Whois::class, 3);
    }
}
