<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Context\User\Domain\Model\User;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Password;
use Tests\Feature\MakesRequestsFromPage;
use Illuminate\Auth\Events\PasswordReset;

class ResetPasswordTest extends TestCase
{
    use DatabaseTransactions;
    use MakesRequestsFromPage;

    protected function getValidToken(CanResetPasswordContract $user): string
    {
        return Password::broker()->createToken($user);
    }

    protected function getInvalidToken(): string
    {
        return 'invalid-token';
    }

    protected function passwordResetGetRoute(string $token): string
    {
        return route('password.reset', $token);
    }

    protected function passwordResetPostRoute(): string
    {
        return '/password/reset';
    }

    protected function successfulPasswordResetRoute(): string
    {
        return route('home');
    }

    protected function guestMiddlewareRoute(): string
    {
        return route('home');
    }


    public function testUserCannotViewAPasswordResetFormWhenAuthenticated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get($this->passwordResetGetRoute($this->getValidToken($user)));

        $response->assertRedirect($this->guestMiddlewareRoute());
    }

    public function testUserCanResetPasswordWithValidToken(): void
    {
        Event::fake();
        $user = User::factory()->create();

        $this->withoutExceptionHandling();
        $response = $this->post($this->passwordResetPostRoute(), [
            'token' => $this->getValidToken($user),
            'email' => $user->email,
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        $response->assertRedirect($this->successfulPasswordResetRoute());
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('new-awesome-password', $user->fresh()->password));
        $this->assertAuthenticatedAs($user);
        Event::assertDispatched(PasswordReset::class, static function ($e) use ($user) {
            return $e->user->id === $user->id;
        });
    }

    public function testUserCannotResetPasswordWithInvalidToken(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);

        $response = $this->fromPage($this->passwordResetGetRoute($this->getInvalidToken()))->post($this->passwordResetPostRoute(), [
            'token' => $this->getInvalidToken(),
            'email' => $user->email,
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        $response->assertRedirect($this->passwordResetGetRoute($this->getInvalidToken()));
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
        $this->assertGuest();
    }

    public function testUserCannotResetPasswordWithoutProvidingANewPassword(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);

        $response = $this->fromPage($this->passwordResetGetRoute($token = $this->getValidToken($user)))->post($this->passwordResetPostRoute(), [
            'token' => $token,
            'email' => $user->email,
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect($this->passwordResetGetRoute($token));
        $response->assertSessionHasErrors('password');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
        $this->assertGuest();
    }

    public function testUserCannotResetPasswordWithoutProvingAnEmail(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);

        $response = $this->fromPage($this->passwordResetGetRoute($token = $this->getValidToken($user)))->post($this->passwordResetPostRoute(), [
            'token' => $token,
            'email' => '',
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        $response->assertRedirect($this->passwordResetGetRoute($token));
        $response->assertSessionHasErrors('email');
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
        $this->assertGuest();
    }
}
