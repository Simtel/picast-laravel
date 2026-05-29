<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Infrastructure\Http\Controllers\Api;

use App\Context\Tournaments\Application\Query\GetTournamentDetailQuery;
use App\Context\Tournaments\Application\Query\GetTournamentsQuery;
use App\Context\Tournaments\Application\QueryHandler\GetTournamentDetailQueryHandler;
use App\Context\Tournaments\Application\QueryHandler\GetTournamentsQueryHandler;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Tournaments",
    description: "API для управления турнирами"
)]
#[OA\Schema(
    schema: "Tournament",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "title", type: "string", example: "Турнир по шахматам"),
        new OA\Property(property: "link", type: "string", format: "uri", example: "https://example.com/tournament/1"),
        new OA\Property(property: "date", type: "string", format: "date", example: "2026-06-01"),
        new OA\Property(property: "date_end", type: "string", format: "date", example: "2026-06-07"),
        new OA\Property(property: "city", type: "string", example: "Москва"),
        new OA\Property(property: "organizer", type: "string", example: "Шахматный клуб"),
        new OA\Property(property: "guid", type: "string", example: "abc123"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-05-01T10:00:00Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-05-01T10:00:00Z"),
    ]
)]
#[OA\Schema(
    schema: "TournamentDetail",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "title", type: "string", example: "Турнир по шахматам"),
        new OA\Property(property: "link", type: "string", format: "uri", example: "https://example.com/tournament/1"),
        new OA\Property(property: "date", type: "string", format: "date", example: "2026-06-01"),
        new OA\Property(property: "date_end", type: "string", format: "date", example: "2026-06-07"),
        new OA\Property(property: "city", type: "string", example: "Москва"),
        new OA\Property(property: "organizer", type: "string", example: "Шахматный клуб"),
        new OA\Property(property: "guid", type: "string", example: "abc123"),
        new OA\Property(property: "groups", type: "array", items: new OA\Items(type: "object")),
    ]
)]
final class TournamentsController extends Controller
{
    public function __construct(
        private readonly GetTournamentsQueryHandler $tournamentsQueryHandler,
        private readonly GetTournamentDetailQueryHandler $tournamentDetailQueryHandler,
    ) {
    }

    #[OA\Get(
        path: '/api/v1/tournaments',
        summary: 'Получить список турниров',
        security: [['sanctum' => []]],
        tags: ['Tournaments'],
        parameters: [
            new OA\Parameter(name: 'city', description: 'Фильтр по городу', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_by', description: 'Сортировка', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort_order', description: 'Направление сортировки', in: 'query', schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список турниров',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Tournament')
                )
            )
        ]
    )]
    public function index(\Illuminate\Http\Request $request): AnonymousResourceCollection
    {
        $query = GetTournamentsQuery::fromRequest($request->query());
        $response = $this->tournamentsQueryHandler->handle($query);

        $collection = AnonymousResourceCollection::collection($response->tournaments);

        return $collection->additional([
            'cities' => $response->cities,
            'selectedCity' => $response->selectedCity,
        ]);
    }

    #[OA\Get(
        path: '/api/v1/tournaments/{id}',
        summary: 'Получить детали турнира',
        security: [['sanctum' => []]],
        tags: ['Tournaments'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID турнира',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(name: 'search', description: 'Поиск по группам', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'number', description: 'Номер группы', in: 'query', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Информация о турнире с группами',
                content: new OA\JsonContent(ref: '#/components/schemas/TournamentDetail')
            ),
            new OA\Response(response: 404, description: 'Турнир не найден')
        ]
    )]
    public function show(\Illuminate\Http\Request $request, int $id): JsonResponse
    {
        try {
            $query = new GetTournamentDetailQuery(
                id: $id,
                search: $request->input('search', ''),
                number: $request->integer('number', 0),
                sortBy: $request->input('sort_by', 'number'),
                sortOrder: $request->input('sort_order', 'asc'),
                page: $request->integer('page', 1),
            );

            $response = $this->tournamentDetailQueryHandler->handle($query);

            return response()->json([
                'tournament' => $response->tournament,
                'groups' => $response->groups,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Турнир не найден'], 404);
        }
    }
}
