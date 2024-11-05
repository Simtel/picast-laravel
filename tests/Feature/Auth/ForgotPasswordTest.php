<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Context\User\Domain\Model\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use stdClass;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\MakesRequestsFromPage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;

class ForgotPasswordTest extends TestCase
{
    use DatabaseTransactions;
    use MakesRequestsFromPage;

    protected function passwordRequestRoute(): string
    {
        return route('password.request');
    }

    protected function passwordEmailGetRoute(): string
    {
        return route('password.email');
    }

    protected function passwordEmailPostRoute(): string
    {
        return route('password.email');
    }

    protected function guestMiddlewareRoute(): string
    {
        return route('home');
    }



    public function testUserCannotViewAnEmailPasswordFormWhenAuthenticated(): void
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)->get($this->passwordRequestRoute());

        $response->assertRedirect($this->guestMiddlewareRoute());
    }

    public function testUserReceivesAnEmailWithAPasswordResetLink(): void
    {
        Notification::fake();
        $user = User::factory()->create([
            'email' => 'john@example.com',
        ]);

        $response = $this->post($this->passwordEmailPostRoute(), [
            'email' => 'john@example.com',
        ]);
        /** @var stdClass $token */
        $token = DB::table('password_resets')->first();
        $this->assertNotNull($token);
        Notification::assertSentTo($user, ResetPassword::class, static function ($notification, $channels) use ($token) {
            return Hash::check($notification->token, $token->token) === true;
        });
    }

    public function testUserDoesNotReceiveEmailWhenNotRegistered(): void
    {
        Notification::fake();

        $response = $this->fromPage($this->passwordEmailGetRoute())->post($this->passwordEmailPostRoute(), [
            'email' => 'nobody@example.com',
        ]);

        $response->assertRedirect($this->passwordEmailGetRoute());
        $response->assertSessionHasErrors('email');
        Notification::assertNotSentTo(User::factory()->create(['email' => 'nobody@example.com']), ResetPassword::class);
    }

    public function testEmailIsRequired(): void
    {
        $response = $this->fromPage($this->passwordEmailGetRoute())->post($this->passwordEmailPostRoute(), []);

        $response->assertRedirect($this->passwordEmailGetRoute());
        $response->assertSessionHasErrors('email');
    }

    public function testEmailIsAValidEmail(): void
    {
        $response = $this->fromPage($this->passwordEmailGetRoute())->post($this->passwordEmailPostRoute(), [
            'email' => 'invalid-email',
        ]);

        $response->assertRedirect($this->passwordEmailGetRoute());
        $response->assertSessionHasErrors('email');
    }
}
