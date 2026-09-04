<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Application\Query;

/**
 * Query для получения деталей турнира с группами.
 */
final readonly class GetTournamentDetailQuery
{
    /**
     * @param int $id ID турнира
     * @param string|null $search Поиск по названию группы
     * @param int $number Фильтр по номеру группы
     * @param string $sortBy Поле для сортировки групп
     * @param string $sortOrder Направление сортировки групп
     * @param int $page Номер страницы
     */
    public function __construct(
        public int $id,
        public ?string $search = null,
        public int $number = 0,
        public string $sortBy = 'number',
        public string $sortOrder = 'asc',
        public int $page = 1,
    ) {
    }

    /**
     * @param int $id ID турнира из роута
     * @param array{
     *     search?: string|null,
     *     number?: int|string,
     *     sort_by?: string,
     *     sort_order?: string,
     *     page?: int|string
     * } $params
     */
    public static function fromRequest(int $id, array $params): self
    {
        return new self(
            id: $id,
            search: $params['search'] ?? null,
            number: (int)($params['number'] ?? 0),
            sortBy: $params['sort_by'] ?? 'number',
            sortOrder: $params['sort_order'] ?? 'asc',
            page: (int)($params['page'] ?? 1),
        );
    }
}
