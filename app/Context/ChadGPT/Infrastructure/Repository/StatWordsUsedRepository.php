<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Infrastructure\Repository;

use App\Context\ChadGPT\Domain\Model\ChadGptConversationWordStat;
use App\Context\User\Domain\Model\User;
use Illuminate\Support\Collection;

class StatWordsUsedRepository
{
    /**
     * @param User $user
     * @return Collection<int, ChadGptConversationWordStat>
     */
    public function findByUser(User $user): Collection
    {
        return ChadGptConversationWordStat::where(['user_id' => $user->getId()])->get();
    }
}
