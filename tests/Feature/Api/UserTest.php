<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

final class UserTest extends TestCase
{
    /**
     *
     * @return void
     */
    public function test_user_current_not_auth(): void
    {
        $response = $this->json('get', route('api.user.current'));
        $response->assertStatus(401);
        $response->assertJson(static fn (AssertableJson $json) => $json->where('message', 'Unauthenticated'));
    }

    /**
     *
     * @return void
     */
    public function test_user_current(): void
    {
        $user = $this->getAdminUser();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->get(
            route('api.user.current'),
            [
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertOk();

        $response->assertJson(['id' => true]);
    }
}
