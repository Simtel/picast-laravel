<?php

declare(strict_types=1);

namespace Tests\Feature\Common;

use App\Context\User\Domain\Model\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class SettingsControllerTest extends TestCase
{
    public function test_personal_settings_page(): void
    {
        $this->authUserWithPermissions([], []);

        $response = $this->get(route('settings'));
        $response->assertStatus(200);
        $response->assertViewIs('personal.settings');
        $response->assertSee('API Токен');
        $response->assertSee('Личная информация');
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

    public function test_personal_update_profile(): void
    {
        $this->authUserWithPermissions([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'birth_date' => '1990-01-01'
        ], []);

        /** @var User $user */
        $user = $this->getAuthUser();

        $response = $this->post(route('settings.profile'), [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'birth_date' => '1985-12-25'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('settings'));
        $response->assertSessionHas('success', 'Профиль успешно обновлен!');

        $user->refresh();
        self::assertEquals('New Name', $user->name);
        self::assertEquals('new@example.com', $user->email);
        self::assertEquals('1985-12-25', $user->birth_date->format('Y-m-d'));
    }

    public function test_personal_update_profile_with_null_birth_date(): void
    {
        $this->authUserWithPermissions([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'birth_date' => '1990-01-01'
        ], []);

        /** @var User $user */
        $user = $this->getAuthUser();

        $response = $this->post(route('settings.profile'), [
            'name' => 'Test User Updated',
            'email' => 'test@example.com',
            'birth_date' => ''
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('settings'));

        $user->refresh();
        self::assertEquals('Test User Updated', $user->name);
        self::assertNull($user->birth_date);
    }

    public function test_personal_update_profile_validation_errors(): void
    {
        $this->authUserWithPermissions(['email' => 'existing@example.com'], []);

        // Create another user with email that we'll try to use
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->post(route('settings.profile'), [
            'name' => '', // Required field empty
            'email' => 'taken@example.com', // Email already taken
            'birth_date' => 'invalid-date' // Invalid date format
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name', 'email', 'birth_date']);
    }

    public function test_personal_update_profile_birth_date_future_validation(): void
    {
        $this->authUserWithPermissions(['email' => 'test@example.com'], []);

        $futureDate = now()->addYear()->format('Y-m-d');

        $response = $this->post(route('settings.profile'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'birth_date' => $futureDate
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['birth_date']);
    }
}
