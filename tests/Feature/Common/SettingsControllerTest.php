<?php

declare(strict_types=1);

namespace Tests\Feature\Common;

use App\Context\User\Domain\Model\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SettingsControllerTest extends TestCase
{
    public function test_personal_settings_page(): void
    {
        $this->authUserWithPermissions([], []);

        $response = $this->get(route('settings'));
        $response->assertStatus(200);
        $response->assertViewIs('personal.settings');
        $response->assertSee('API Токен');
    }

    public function test_personal_update_token(): void
    {
        $this->authUserWithPermissions([], []);

        /** @var User $user */
        $user = $this->getAuthUser();
        self::assertNull($user->getApiToken());
        $response = $this->post(route('settings.token'));
        $response->assertStatus(302);
        $response->assertRedirect(route('settings'));
        self::assertNotNull($user->getApiToken());
    }

    public function test_personal_change_password(): void
    {
        $password = Hash::make('testPassword');

        $this->authUserWithPermissions(['password' => $password], []);

        /** @var User $user */
        $user = $this->getAuthUser();
        self::assertEquals($password, $user->password);
        $response = $this->post(route('settings.password', ['password' => 'testNewPassword']));
        $response->assertStatus(302);
        $response->assertRedirect(route('settings'));
        /** @var User $actualUser */
        $actualUser = User::find($user->id);
        self::assertNotEquals($password, $actualUser->password);
    }


}
