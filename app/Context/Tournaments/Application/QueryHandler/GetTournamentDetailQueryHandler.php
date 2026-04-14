<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Application\QueryHandler;

use App\Context\Tournaments\Application\Dto\TournamentDetailDto;
use App\Context\Tournaments\Application\Dto\TournamentGroupDto;
use App\Context\Tournaments\Application\Query\GetTournamentDetailQuery;
use App\Context\Tournaments\Application\Query\GetTournamentDetailQueryResponse;
use App\Context\Tournaments\Domain\Model\Tournament;
use App\Context\Tournaments\Domain\Model\TournamentGroup;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

/**
 * Handler для обработки запроса получения деталей турнира.
 * Реализует Query Side CQRS паттерна.
 */
final class GetTournamentDetailQueryHandler
{
    private const int GROUPS_PER_PAGE = 25;

    /**
     * @param GetTournamentDetailQuery $query
     * @return GetTournamentDetailQueryResponse
     */
    public function handle(GetTournamentDetailQuery $query): GetTournamentDetailQueryResponse
    {
        // Получение турнира
        $tournament = Tournament::findOrFail($query->id);

        // Построение запроса для групп
        $groupsQuery = $tournament->groups();

        // Фильтрация по поиску
        if ($query->search !== null && $query->search !== '') {
            $groupsQuery->where('name', 'like', "%{$query->search}%");
        }

        // Фильтрация по номеру
        if ($query->number > 0) {
            $groupsQuery->where('number', $query->number);
        }

        // Сортировка
        $groupsQuery->orderBy($query->sortBy, $query->sortOrder);

        // Пагинация
        $paginatedGroups = $groupsQuery->paginate(self::GROUPS_PER_PAGE);

        // Преобразование групп в DTO
        $groupsDto = $paginatedGroups->map(
            static fn (TournamentGroup $group): TournamentGroupDto =>
            TournamentGroupDto::fromArray([
                'id' => $group->id,
                'tournament_id' => $group->tournament_id,
                'number' => $group->number,
                'name' => $group->name,
                'registrations' => $group->registrations,
            ])
        );

        // Создание новой LengthAwarePaginator с DTO
        $groupsPaginator = new LengthAwarePaginator(
            $groupsDto,
            $paginatedGroups->total(),
            $paginatedGroups->perPage(),
            $paginatedGroups->currentPage(),
            [
                'path' => Request::url(),
                'pageName' => 'page',
            ]
        );

        // Создание DTO турнира
        $tournamentDto = TournamentDetailDto::fromArray([
            'id' => $tournament->id,
            'title' => $tournament->title,
            'link' => $tournament->link,
            'date' => $tournament->date?->toDateString(),
            'date_end' => $tournament->date_end?->toDateString(),
            'city' => $tournament->city,
            'organizer' => $tournament->organizer,
            'guid' => $tournament->guid,
        ], []);

        return new GetTournamentDetailQueryResponse(
            tournament: $tournamentDto,
            groups: $groupsPaginator
        );
    }
}
