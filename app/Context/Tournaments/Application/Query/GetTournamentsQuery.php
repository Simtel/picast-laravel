<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Application\Query;

use App\Context\Tournaments\Infrastructure\Controllers\TournamentController;

/**
 * Query для получения списка турниров с фильтрацией и сортировкой.
 */
final readonly class GetTournamentsQuery
{
    /**
     * @param string|null $city Фильтр по городу
     * @param string $sortBy Поле для сортировки
     * @param string $sortOrder Направление сортировки (asc/desc)
     * @param int $page Номер страницы
     */
    public function __construct(
        public ?string $city = null,
        public string $sortBy = 'date',
        public string $sortOrder = 'asc',
        public int $page = 1,
        public int $perPage = TournamentController::GROUPS_PER_PAGE,
    ) {
    }

    /**
     * @param array{
     *     city?: string|null,
     *     sort_by?: string,
     *     sort_order?: string,
     *     page?: int
     * } $query
     */
    public static function fromRequest(array $query): self
    {
        return new self(
            city: $query['city'] ?? null,
            sortBy: $query['sort_by'] ?? 'date',
            sortOrder: $query['sort_order'] ?? 'asc',
            page:  (int)($query['page'] ?? 1),
        );
    }
}
