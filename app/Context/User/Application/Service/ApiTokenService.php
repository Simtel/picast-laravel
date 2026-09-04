<?php

declare(strict_types=1);

namespace App\Context\User\Application\Service;

use App\Context\User\Domain\Model\User;
use Illuminate\Support\Facades\Log;

final class ApiTokenService
{
    public function create(User $user): string
    {
        $token = $user->createToken('api-token');

        Log::info('[ApiTokenService.create] токен создан', ['user' => $user->getId()]);

        return $token->plainTextToken;
    }

    public function delete(User $user, int $tokenId): void
    {
        $user->tokens()->where('id', $tokenId)->delete();

        Log::info('[ApiTokenService.delete] токен удалён', ['user' => $user->getId(), 'token' => $tokenId]);
    }
}
