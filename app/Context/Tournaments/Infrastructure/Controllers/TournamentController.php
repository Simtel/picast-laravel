<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Infrastructure\Controllers;

use App\Context\Tournaments\Application\Query\GetTournamentDetailQuery;
use App\Context\Tournaments\Application\Query\GetTournamentsQuery;
use App\Context\Tournaments\Application\QueryHandler\GetTournamentDetailQueryHandler;
use App\Context\Tournaments\Application\QueryHandler\GetTournamentsQueryHandler;
use App\Context\Tournaments\Domain\Model\Tournament;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Контроллер для управления турнирами.
 * Использует CQRS паттерн через Query Handlers.
 */
final class TournamentController
{
    public const int GROUPS_PER_PAGE = 25;

    public function __construct(
        private readonly GetTournamentsQueryHandler $tournamentsQueryHandler,
        private readonly GetTournamentDetailQueryHandler $tournamentDetailQueryHandler,
    ) {
    }

    /**
     * Отображение списка турниров.
     *
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request): Factory|View
    {
        $query = GetTournamentsQuery::fromRequest($request->query());
        $response = $this->tournamentsQueryHandler->handle($query);

        return view('tournaments.index', [
            'tournaments' => $response->tournaments,
            'cities' => $response->cities,
            'selectedCity' => $response->selectedCity,
            'sortBy' => $query->sortBy,
            'sortOrder' => $query->sortOrder,
        ]);
    }

    /**
     * Отображение деталей турнира.
     *
     * @param Request $request
     * @param int $id
     * @return Factory|View
     * @throws ModelNotFoundException<Tournament>
     */
    public function show(Request $request, int $id): Factory|View
    {
        $query = new GetTournamentDetailQuery(
            id: $id,
            search: $request->input('search', ''),
            number: $request->integer('number', 0),
            sortBy: $request->input('sort_by', 'number'),
            sortOrder: $request->input('sort_order', 'asc'),
            page: $request->integer('page', 1),
        );

        $response = $this->tournamentDetailQueryHandler->handle($query);

        return view('tournaments.show', [
            'tournament' => $response->tournament,
            'groups' => $response->groups,
            'sortBy' => $query->sortBy,
            'sortOrder' => $query->sortOrder,
        ]);
    }
}
