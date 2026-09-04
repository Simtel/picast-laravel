<?php

declare(strict_types=1);

namespace App\Context\User\Application\Service;

use App\Context\User\Domain\Model\User;
use Illuminate\Support\Facades\Log;

final class ProfileUpdateService
{
    /**
     * @param User $user
     * @param array{name: string, email: string, birth_date?: string|null} $data
     * @return void
     */
    public function update(User $user, array $data): void
    {
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'birth_date' => $data['birth_date'] ?? null,
        ]);

        Log::info('[ProfileUpdateService.update] профиль обновлён', ['user' => $user->getId()]);
    }
}
