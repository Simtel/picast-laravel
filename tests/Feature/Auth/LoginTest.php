<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Context\User\Domain\Model\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Session\Store;
use Illuminate\Support\Collection;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;
use Tests\Feature\MakesRequestsFromPage;

class LoginTest extends TestCase
{
    use DatabaseTransactions;
    use MakesRequestsFromPage;

    protected function successfulLoginRoute(): string
    {
        return route('personal');
    }

    protected function loginGetRoute(): string
    {
        return route('login');
    }

    protected function loginPostRoute(): string
    {
        return route('login');
    }

    protected function logoutRoute(): string
    {
        return route('logout');
    }

    protected function successfulLogoutRoute(): string
    {
        return '/';
    }

    protected function guestMiddlewareRoute(): string
    {
        return route('home');
    }

    public function testUserCanViewALoginForm(): void
    {
        $response = $this->get($this->loginGetRoute());

        $response->assertSuccessful();
        $response->assertViewIs('auth.login');
    }

    public function testUserCannotViewALoginFormWhenAuthenticated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get($this->loginGetRoute());

        $response->assertRedirect($this->guestMiddlewareRoute());
    }

    public function testUserCanLoginWithCorrectCredentials(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'i-love-laravel'),
        ]);

        $response = $this->post($this->loginPostRoute(), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect($this->successfulLoginRoute());
        $this->assertAuthenticatedAs($user);
    }

    public function testUserCannotLoginWithIncorrectPassword(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('i-love-laravel'),
        ]);

        $response = $this->fromPage($this->loginGetRoute())->post($this->loginPostRoute(), [
            'email' => $user->email,
            'password' => 'invalid-password',
        ]);

        $response->assertRedirect($this->loginGetRoute());
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    public function testUserCannotLoginWithEmailThatDoesNotExist(): void
    {
        $response = $this->fromPage($this->loginGetRoute())->post($this->loginPostRoute(), [
            'email' => 'nobody@example.com',
            'password' => 'invalid-password',
        ]);

        $response->assertRedirect($this->loginGetRoute());
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    public function testUserCanLogout(): void
    {
        $this->be(User::factory()->create());

        $response = $this->post($this->logoutRoute());

        $response->assertRedirect($this->successfulLogoutRoute());
        $this->assertGuest();
    }

    public function testUserCannotLogoutWhenNotAuthenticated(): void
    {
        $response = $this->post($this->logoutRoute());

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function testUserCannotMakeMoreThanFiveAttemptsInOneMinute(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'i-love-laravel'),
        ]);

        foreach (range(0, 5) as $_) {
            $response = $this->fromPage($this->loginGetRoute())->post($this->loginPostRoute(), [
                'email' => $user->email,
                'password' => 'invalid-password',
            ]);
        }

        $response->assertRedirect($this->loginGetRoute());
        $response->assertSessionHasErrors('email');
        /** @var RedirectResponse $baseResponse */
        $baseResponse = $response->baseResponse;
        /** @var Store $store */
        $store = $baseResponse->getSession();

        /** @var ViewErrorBag $errors */
        $errors = $store->get('errors');
        $collectErrors = $errors->getBag('default')->get('email');
        /** @var Collection<int, string> $collect  */
        $collect = collect(
            $collectErrors
        );
        $this->assertContains(
            'Слишком много попыток входа. Пожалуйста, попробуйте еще раз через 59 секунд.',
            $collect->all(),
        );
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }
}
