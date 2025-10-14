<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Infrastructure\Repository;

use App\Context\User\Domain\Model\User;
use App\Models\ChadGptConversation;
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
}
