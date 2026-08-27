<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Context\User\Domain\Model\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

final class VerificationTest extends TestCase
{
    use DatabaseTransactions;

    protected function verificationNoticeRoute(): string
    {
        return route('verification.notice');
    }

    protected function verificationResendRoute(): string
    {
        return route('verification.resend');
    }

    protected function successfulVerificationRoute(): string
    {
        return '/';
    }

    public function testUserCanViewTheVerificationNoticeWhenNotAuthenticated(): void
    {
        $response = $this->get($this->verificationNoticeRoute());

        $response->assertRedirect(route('login'));
    }

    public function testUserCanViewTheVerificationNoticeWhenEmailIsNotVerified(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get($this->verificationNoticeRoute());

        $response->assertSuccessful();
        $response->assertViewIs('auth.verify');
    }

    public function testUserCannotViewTheVerificationNoticeWhenEmailIsAlreadyVerified(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get($this->verificationNoticeRoute());

        $response->assertRedirect($this->successfulVerificationRoute());
    }

    public function testUserCanVerifyEmailWithAValidSignedUrl(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get($this->validVerificationUrl($user));

        $response->assertRedirect($this->successfulVerificationRoute());
        $response->assertSessionHas('verified', true);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function testUserCannotVerifyEmailWithAnInvalidHash(): void
    {
        $user = User::factory()->create();

        $url = URL::signedRoute(
            'verification.verify',
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $response = $this->actingAs($user)->get($url);

        $response->assertForbidden();
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function testUserCannotVerifyEmailWithAnExpiredSignature(): void
    {
        $user = User::factory()->create();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinute(),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(403);
    }

    public function testUserCannotVerifyEmailWhenAlreadyVerified(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get($this->validVerificationUrl($user));

        $response->assertRedirect($this->successfulVerificationRoute());
        $response->assertSessionMissing('verified');
    }

    public function testUserCanResendTheVerificationEmailWhenNotVerified(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from($this->verificationNoticeRoute())
            ->post($this->verificationResendRoute());

        $response->assertRedirect($this->verificationNoticeRoute());
        $response->assertSessionHas('resent', true);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function testUserCannotResendTheVerificationEmailWhenAlreadyVerified(): void
    {
        Notification::fake();
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->post($this->verificationResendRoute());

        $response->assertRedirect($this->successfulVerificationRoute());
        Notification::assertNotSentTo($user, VerifyEmail::class);
    }

    protected function validVerificationUrl(User $user): string
    {
        return URL::signedRoute(
            'verification.verify',
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
    }
}
