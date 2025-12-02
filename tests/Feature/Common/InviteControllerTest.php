<?php

declare(strict_types=1);

namespace Tests\Feature\Common;

use App\Context\Common\Domain\Models\InviteCode;
use App\Context\User\Infrastructure\Mail\InviteUserNotify;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

final class InviteControllerTest extends TestCase
{
    public function test_personal_invite_page(): void
    {
        $this->authUserWithPermissions([], ['invite user']);

        $response = $this->get(route('invite'));
        $response->assertStatus(200);
        $response->assertViewIs('personal.invite');
        $response->assertSee('Пригласить участника');
        $response->assertSee('Имя');
        $response->assertSee('E-mail');
        $response->assertSee('Пригласить пользователя');
    }


    public function test_personal_invite_user(): void
    {
        Mail::fake();
        $this->authUserWithPermissions([], ['invite user']);

        $response = $this->post(route('invite.user'), ['name' => 'test user', 'email' => 'test@test.com']);
        $response->assertRedirect(route('personal'));

        Mail::assertSentCount(1);
        Mail::assertSent(InviteUserNotify::class, static function (InviteUserNotify $mail) {
            $mail->assertTo('test@test.com');
            return $mail->assertHasSubject('Вы приглашены в сервис');

        });
        $this->assertDatabaseCount(InviteCode::class, 1);
    }


}
