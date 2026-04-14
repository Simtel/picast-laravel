<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Application\Query;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Ответ для запроса списка турниров.
 */
final readonly class GetTournamentsQueryResponse
{
    /**
     * @param LengthAwarePaginator<int, \App\Context\Tournaments\Application\Dto\TournamentListDto> $tournaments
     * @param string[] $cities Список уникальных городов
     * @param string|null $selectedCity Выбранный город
     */
    public function __construct(
        public LengthAwarePaginator $tournaments,
        public array $cities,
        public ?string $selectedCity,
    ) {
    }
}
