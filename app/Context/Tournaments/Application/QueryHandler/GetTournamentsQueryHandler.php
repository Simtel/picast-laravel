<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Application\QueryHandler;

use App\Context\Tournaments\Application\Data\TournamentListData;
use App\Context\Tournaments\Application\Query\GetTournamentsQuery;
use App\Context\Tournaments\Application\Query\GetTournamentsQueryResponse;
use App\Context\Tournaments\Domain\Model\Tournament;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;

/**
 * Handler для обработки запроса получения списка турниров.
 * Реализует Query Side CQRS паттерна.
 */
final class GetTournamentsQueryHandler
{
    /**
     * @param GetTournamentsQuery $query
     * @return GetTournamentsQueryResponse
     */
    public function handle(GetTournamentsQuery $query): GetTournamentsQueryResponse
    {
        $queryBuilder = Tournament::query()
            ->whereDate('date', '>', Carbon::now());

        // Фильтрация по городу
        if ($query->city !== null && $query->city !== '') {
            $queryBuilder->where('city', $query->city);
        }

        // Сортировка
        $queryBuilder->orderBy($query->sortBy, $query->sortOrder);

        // Пагинация
        $paginatedTournaments = $queryBuilder->paginate($query->perPage);

        // Преобразование в Data объекты
        $tournamentsData = $paginatedTournaments->map(
            static fn (Tournament $tournament): TournamentListData =>
            TournamentListData::from([
                'id' => $tournament->id,
                'title' => $tournament->title,
                'link' => $tournament->link,
                'date' => $tournament->date?->toDateString(),
                'dateEnd' => $tournament->date_end?->toDateString(),
                'city' => $tournament->city,
                'organizer' => $tournament->organizer,
                'guid' => $tournament->guid,
                'groupsCount' => $tournament->groups()->count(),
            ])
        );

        // Создание новой LengthAwarePaginator с Data объектами
        $tournamentsPaginator = new LengthAwarePaginator(
            $tournamentsData,
            $paginatedTournaments->total(),
            $paginatedTournaments->perPage(),
            $paginatedTournaments->currentPage(),
            [
                'path' => Request::url(),
                'pageName' => 'page',
            ]
        );

        // Получение списка городов
        $citiesQuery = Tournament::query()
            ->whereDate('date', '>', Carbon::now())
            ->pluck('city')
            ->unique()
            ->filter()
            ->sort();

        return new GetTournamentsQueryResponse(
            tournaments: $tournamentsPaginator,
            cities: $citiesQuery->values()->toArray(),
            selectedCity: $query->city
        );
    }
}
