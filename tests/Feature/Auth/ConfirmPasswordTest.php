<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Context\User\Domain\Model\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

final class ConfirmPasswordTest extends TestCase
{
    use DatabaseTransactions;

    protected function confirmPasswordGetRoute(): string
    {
        return route('password.confirm');
    }

    protected function confirmPasswordPostRoute(): string
    {
        return route('password.confirm');
    }

    protected function redirectAfterConfirmationRoute(): string
    {
        return '/';
    }

    public function testUserCanViewTheConfirmPasswordFormWhenAuthenticated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get($this->confirmPasswordGetRoute());

        $response->assertSuccessful();
        $response->assertViewIs('auth.passwords.confirm');
    }

    public function testUserCannotViewTheConfirmPasswordFormWhenNotAuthenticated(): void
    {
        $response = $this->get($this->confirmPasswordGetRoute());

        $response->assertRedirect(route('login'));
    }

    public function testUserCanConfirmTheirPasswordWithTheCorrectPassword(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'i-love-laravel'),
        ]);

        $response = $this->actingAs($user)->post($this->confirmPasswordPostRoute(), [
            'password' => $password,
        ]);

        $response->assertRedirect($this->redirectAfterConfirmationRoute());
        $this->assertNotNull(session('auth.password_confirmed_at'));
    }

    public function testUserCannotConfirmPasswordWithAWrongPassword(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('i-love-laravel'),
        ]);

        $response = $this->actingAs($user)->post($this->confirmPasswordPostRoute(), [
            'password' => 'invalid-password',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertNull(session('auth.password_confirmed_at'));
    }

    public function testPasswordIsRequiredToConfirm(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post($this->confirmPasswordPostRoute(), []);

        $response->assertSessionHasErrors('password');
    }
}
