<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
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
        $response = $this->get(route('api.domains.index'), ['Authorization' => 'Bearer '.User::find(1)?->api_token]);
        $response->assertOk();

        $response->assertJson(
            fn (AssertableJson $json) => $json->whereType('data', 'array')
        );
    }
}
