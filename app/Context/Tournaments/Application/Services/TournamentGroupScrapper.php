<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Application\Services;

use App\Context\Tournaments\Domain\DTO\TournamentGroupDto;
use App\Context\Tournaments\Domain\Model\Tournament;

class TournamentGroupScrapper
{
    /**
     * @param Tournament $tournament
     * @return TournamentGroupDto[]
     */
    public function getGroups(Tournament $tournament): array
    {
        return [];
    }
}
