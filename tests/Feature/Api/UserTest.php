<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     *
     * @return void
     */
    public function test_user_current_not_auth(): void
    {
        $response = $this->json('get', route('api.user.current'));
        $response->assertStatus(401);
        $response->assertJson(fn (AssertableJson $json) => $json->where('message', 'Unauthenticated'));
    }

    /**
     *
     * @return void
     */
    public function test_user_current(): void
    {
        $response = $this->get(
            route('api.user.current'),
            [
                'Authorization' => 'Bearer '.User::find(1)?->api_token
            ]
        );
        $response->assertOk();

        $response->assertJson(['id' => true]);
    }
}
