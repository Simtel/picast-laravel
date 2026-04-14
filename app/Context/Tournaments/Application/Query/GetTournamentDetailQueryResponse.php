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
     * @param \App\Context\Tournaments\Application\Dto\TournamentDetailDto $tournament
     * @param LengthAwarePaginator<int, \App\Context\Tournaments\Application\Dto\TournamentGroupDto> $groups
     */
    public function __construct(
        public \App\Context\Tournaments\Application\Dto\TournamentDetailDto $tournament,
        public LengthAwarePaginator $groups,
    ) {
    }
}
