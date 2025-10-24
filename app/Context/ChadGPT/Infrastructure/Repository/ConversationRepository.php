<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Infrastructure\Repository;

use App\Context\ChadGPT\Domain\Model\ChadGptConversation;
use App\Context\User\Domain\Model\User;
use Illuminate\Support\Collection;

class ConversationRepository
{
    /**
     * @param User $user
     * @return Collection<int, ChadGptConversation>
     */
    public function findBuUser(User $user): Collection
    {
        return ChadGptConversation::whereUserId($user->getId())
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
    }

    public function deleteByUser(User $user): void
    {
        ChadGptConversation::where('user_id', $user->id)->delete();
    }
}
