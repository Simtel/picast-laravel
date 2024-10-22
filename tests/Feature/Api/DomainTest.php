<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Context\Domains\Domain\Model\Domain;
use Event;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class DomainTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_unauthenticated(): void
    {
        $response = $this->json('get', route('api.domains.index'));
        $response->assertStatus(401);
        $response->assertJson(fn (AssertableJson $json) => $json->where('message', 'Unauthenticated'));
    }

    public function test_domains_list(): void
    {
        $user = $this->getAdminUser();

        Event::fake();
        $domain1 = Domain::factory()->create(['user_id' => $user->getId()]);
        $domain2 = Domain::factory()->create(['user_id' => $user->getId()]);

        $response = $this->get(route('api.domains.index'), ['Authorization' => 'Bearer ' . $user->api_token]);
        $response->assertOk();

        $response->assertJson(
            fn (AssertableJson $json) => $json->whereType('data', 'array')
        );
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data')
                ->has(
                    'data.0',
                    fn (AssertableJson $json) => $json->where('id', $domain1->getId())
                    ->where('name', $domain1->getName())
                    ->etc()
                )
                ->has(
                    'data.1',
                    fn (AssertableJson $json) => $json->where('id', $domain2->getId())
                    ->where('name', $domain2->getName())
                    ->etc()
                )
        );
    }

    public function test_domain_show(): void
    {
        $user = $this->getAdminUser();

        Event::fake();
        $domain = Domain::factory()->create(['user_id' => $user->getId()]);

        $response = $this->get(route('api.domains.show', ['domain' => $domain]), ['Authorization' => 'Bearer ' . $user->api_token]);
        $response->assertOk();

        $response->assertJson(
            fn (AssertableJson $json) => $json->whereType('data', 'array')
        );
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data')
                ->has(
                    'data',
                    fn (AssertableJson $json) => $json->where('id', $domain->getId())
                    ->where('name', $domain->getName())
                    ->etc()
                )
        );
    }

    public function test_domain_create_form(): void
    {
        $user = $this->getAdminUser();

        $response = $this->get(route('api.domains.create', ), ['Authorization' => 'Bearer ' . $user->api_token]);
        $response->assertStatus(403);
        $response->assertJson(fn (AssertableJson $json) => $json->where('message', 'Not action.'));
    }
    public function test_domain_edit_form(): void
    {
        $user = $this->getAdminUser();

        Event::fake();
        $domain = Domain::factory()->create(['user_id' => $user->getId()]);

        $response = $this->get(route('api.domains.edit', ['domain' => $domain]), ['Authorization' => 'Bearer ' . $user->api_token]);
        $response->assertStatus(403);
        $response->assertJson(fn (AssertableJson $json) => $json->where('message', 'Not action.'));
    }
}
