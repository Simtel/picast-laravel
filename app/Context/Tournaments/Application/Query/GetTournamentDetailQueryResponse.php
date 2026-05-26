<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Application\Query;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Ответ для запроса деталей турнира.
 */
final readonly class GetTournamentDetailQueryResponse
{
    /**
     * @param \App\Context\Tournaments\Application\Data\TournamentDetailData $tournament
     * @param LengthAwarePaginator<int, \App\Context\Tournaments\Application\Data\TournamentGroupData> $groups
     */
    public function __construct(
        public \App\Context\Tournaments\Application\Data\TournamentDetailData $tournament,
        public LengthAwarePaginator $groups,
    ) {
    }
}
