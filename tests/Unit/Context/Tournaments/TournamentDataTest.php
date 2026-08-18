<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Tournaments;

use App\Context\Tournaments\Application\Data\TournamentDetailData;
use App\Context\Tournaments\Application\Data\TournamentGroupData;
use App\Context\Tournaments\Application\Data\TournamentListData;
use App\Context\Tournaments\Application\Query\GetTournamentDetailQuery;
use App\Context\Tournaments\Application\Query\GetTournamentDetailQueryResponse;
use App\Context\Tournaments\Application\Query\GetTournamentsQuery;
use App\Context\Tournaments\Application\Query\GetTournamentsQueryResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

final class TournamentDataTest extends TestCase
{
    public function test_tournament_detail_data_from_array_with_groups(): void
    {
        $data = TournamentDetailData::fromArray(
            [
                'id' => 1,
                'title' => 'Test',
                'link' => 'https://example.com',
                'date' => '2026-06-01',
                'date_end' => '2026-06-03',
                'city' => 'Москва',
                'organizer' => 'Club',
                'guid' => 'abc',
            ],
            [
                [
                    'id' => 1,
                    'tournamentId' => 1,
                    'number' => 1,
                    'name' => 'Group 1',
                    'registrations' => 10,
                ],
            ]
        );

        $this->assertEquals(1, $data->id);
        $this->assertEquals('Test', $data->title);
        $this->assertEquals('2026-06-01', $data->date?->toDateString());
        $this->assertEquals('2026-06-03', $data->dateEnd?->toDateString());
        $this->assertEquals('Москва', $data->city);
        $this->assertEquals('Club', $data->organizer);
        $this->assertEquals('abc', $data->guid);
        $this->assertCount(1, $data->groups);
        $this->assertInstanceOf(TournamentGroupData::class, $data->groups->first());
        $this->assertEquals('Group 1', $data->groups->first()->name);
    }

    public function test_tournament_detail_data_from_array_without_optionals(): void
    {
        $data = TournamentDetailData::fromArray([
            'id' => 2,
            'title' => 'Test',
            'link' => 'https://example.com',
            'guid' => 'abc',
        ]);

        $this->assertNull($data->date);
        $this->assertNull($data->dateEnd);
        $this->assertNull($data->city);
        $this->assertNull($data->organizer);
        $this->assertCount(0, $data->groups);
    }

    public function test_tournament_list_data_from(): void
    {
        $data = TournamentListData::from([
            'id' => 3,
            'title' => 'List',
            'link' => 'https://example.com',
            'date' => '2026-06-01',
            'dateEnd' => null,
            'city' => null,
            'organizer' => null,
            'guid' => 'abc',
            'groupsCount' => 5,
        ]);

        $this->assertEquals(3, $data->id);
        $this->assertEquals('List', $data->title);
        $this->assertEquals(5, $data->groupsCount);
        $this->assertNull($data->dateEnd);
    }

    public function test_tournament_group_data_from(): void
    {
        $data = TournamentGroupData::from([
            'id' => 4,
            'tournamentId' => 1,
            'number' => 2,
            'name' => 'Group',
            'registrations' => 8,
        ]);

        $this->assertEquals(4, $data->id);
        $this->assertEquals(1, $data->tournamentId);
        $this->assertEquals(2, $data->number);
        $this->assertEquals('Group', $data->name);
        $this->assertEquals(8, $data->registrations);
    }

    public function test_get_tournaments_query_defaults(): void
    {
        $query = new GetTournamentsQuery();

        $this->assertNull($query->city);
        $this->assertEquals('date', $query->sortBy);
        $this->assertEquals('asc', $query->sortOrder);
        $this->assertEquals(1, $query->page);
        $this->assertGreaterThan(0, $query->perPage);
    }

    public function test_get_tournaments_query_from_request(): void
    {
        $query = GetTournamentsQuery::fromRequest([
            'city' => 'Москва',
            'sort_by' => 'title',
            'sort_order' => 'desc',
            'page' => '3',
        ]);

        $this->assertEquals('Москва', $query->city);
        $this->assertEquals('title', $query->sortBy);
        $this->assertEquals('desc', $query->sortOrder);
        $this->assertEquals(3, $query->page);
    }

    public function test_get_tournaments_query_from_request_uses_defaults(): void
    {
        $query = GetTournamentsQuery::fromRequest([]);

        $this->assertNull($query->city);
        $this->assertEquals('date', $query->sortBy);
        $this->assertEquals('asc', $query->sortOrder);
        $this->assertEquals(1, $query->page);
    }

    public function test_get_tournament_detail_query_defaults(): void
    {
        $query = new GetTournamentDetailQuery(id: 42);

        $this->assertEquals(42, $query->id);
        $this->assertNull($query->search);
        $this->assertEquals(0, $query->number);
        $this->assertEquals('number', $query->sortBy);
        $this->assertEquals('asc', $query->sortOrder);
        $this->assertEquals(1, $query->page);
    }

    public function test_query_responses_can_be_constructed(): void
    {
        $paginator = new LengthAwarePaginator([], 0, 25, 1);

        $listResponse = new GetTournamentsQueryResponse(
            tournaments: $paginator,
            cities: ['Москва'],
            selectedCity: 'Москва',
        );

        $detailData = TournamentDetailData::from([
            'id' => 1,
            'title' => 'Test',
            'link' => 'https://example.com',
            'guid' => 'abc',
            'groups' => [],
        ]);
        $detailResponse = new GetTournamentDetailQueryResponse(
            tournament: $detailData,
            groups: $paginator,
        );

        $this->assertSame($paginator, $listResponse->tournaments);
        $this->assertEquals(['Москва'], $listResponse->cities);
        $this->assertEquals('Москва', $listResponse->selectedCity);
        $this->assertSame($paginator, $detailResponse->groups);
        $this->assertEquals(1, $detailResponse->tournament->id);
    }
}
