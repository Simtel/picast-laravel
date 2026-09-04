<?php

declare(strict_types=1);

namespace App\Context\User\Application\Service;

use App\Context\User\Domain\Model\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class ChangePasswordService
{
    public function change(User $user, string $newPassword): void
    {
        $user->forceFill([
            'password' => Hash::make($newPassword),
        ])->setRememberToken(Str::random(60));
        $user->save();

        event(new PasswordReset($user));

        Log::info('[ChangePasswordService.change] пароль изменён', ['user' => $user->getId()]);
    }
}
