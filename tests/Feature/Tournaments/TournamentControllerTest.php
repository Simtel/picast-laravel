<?php

declare(strict_types=1);

namespace Tests\Feature\Tournaments;

use App\Context\Tournaments\Domain\Model\Tournament;
use App\Context\Tournaments\Domain\Model\TournamentGroup;
use App\Context\Tournaments\Infrastructure\Controllers\TournamentController;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

final class TournamentControllerTest extends TestCase
{
    public function test_user_can_see_tournaments_list(): void
    {
        $this->loginAdmin();

        $tournament1 = Tournament::factory()->create([
            'date' => Carbon::now()->addWeek(),
        ]);
        $tournament2 = Tournament::factory()->create([
            'date' => Carbon::now()->addWeeks(2),
        ]);

        $response = $this->get(route('tournaments.index'));

        $response->assertStatus(200);
        $response->assertViewIs('tournaments.index');
        $response->assertViewHas('tournaments');
        $response->assertSee($tournament1->getTitle());
        $response->assertSee($tournament2->getTitle());
    }

    public function test_tournaments_list_filters_past_events(): void
    {
        $this->loginAdmin();

        $pastTournament = Tournament::factory()->create([
            'date' => Carbon::now()->subWeek(),
        ]);
        $futureTournament = Tournament::factory()->create([
            'date' => Carbon::now()->addWeek(),
        ]);

        $response = $this->get(route('tournaments.index'));
        $tournaments = $response->viewData('tournaments');

        $response->assertStatus(200);
        $this->assertNotContains($pastTournament->getId(), $tournaments->pluck('id')->toArray());
        $this->assertContains($futureTournament->getId(), $tournaments->pluck('id')->toArray());
    }

    public function test_tournaments_list_can_be_sorted_by_date_asc(): void
    {
        $this->loginAdmin();

        $tournament1 = Tournament::factory()->create([
            'date' => Carbon::now()->addWeeks(2),
        ]);
        $tournament2 = Tournament::factory()->create([
            'date' => Carbon::now()->addWeek(),
        ]);

        $response = $this->get(route('tournaments.index', [
            'sort_by' => 'date',
            'sort_order' => 'asc',
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('sortBy', 'date');
        $response->assertViewHas('sortOrder', 'asc');
    }

    public function test_tournaments_list_can_be_sorted_by_date_desc(): void
    {
        $this->loginAdmin();

        Tournament::factory()->create([
            'date' => Carbon::now()->addWeeks(2),
        ]);
        Tournament::factory()->create([
            'date' => Carbon::now()->addWeek(),
        ]);

        $response = $this->get(route('tournaments.index', [
            'sort_by' => 'date',
            'sort_order' => 'desc',
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('sortBy', 'date');
        $response->assertViewHas('sortOrder', 'desc');
    }

    public function test_tournaments_list_pagination_works(): void
    {
        $this->loginAdmin();

        Tournament::factory()->count(30)->create([
            'date' => Carbon::now()->addWeek(),
        ]);

        $response = $this->get(route('tournaments.index'));

        $response->assertStatus(200);
        $response->assertViewHas('tournaments');
        $tournaments = $response->viewData('tournaments');
        $this->assertInstanceOf(LengthAwarePaginator::class, $tournaments);
        $this->assertCount(25, $tournaments->items());
        $this->assertTrue($tournaments->hasMorePages());
    }

    public function test_user_can_see_tournament_details(): void
    {
        $this->loginAdmin();

        $tournament = Tournament::factory()->create([
            'title' => 'Test Tournament',
            'city' => 'Москва',
            'organizer' => 'Dance Club',
        ]);

        $response = $this->get(route('tournaments.show', $tournament->getId()));

        $response->assertStatus(200);
        $response->assertViewIs('tournaments.show');
        $response->assertViewHas('tournament');
        $response->assertSee('Test Tournament');
        $response->assertSee('Москва');
        $response->assertSee('Dance Club');
    }

    public function test_show_returns_404_for_nonexistent_tournament(): void
    {
        $this->loginAdmin();

        $response = $this->get(route('tournaments.show', 999999));

        $response->assertStatus(404);
    }

    public function test_index_shows_tournament_link(): void
    {
        $this->loginAdmin();

        $tournament = Tournament::factory()->create([
            'link' => 'https://example.com/tournament',
        ]);

        $response = $this->get(route('tournaments.index'));

        $response->assertStatus(200);
        $response->assertSee('https://example.com/tournament');
    }

    public function test_index_shows_tournament_with_null_city(): void
    {
        $this->loginAdmin();

        $tournament = Tournament::factory()->create([
            'city' => null,
        ]);

        $response = $this->get(route('tournaments.index'));

        $response->assertStatus(200);
        $response->assertSee($tournament->getTitle());
    }

    public function test_index_shows_tournament_with_null_organizer(): void
    {
        $this->loginAdmin();

        $tournament = Tournament::factory()->create([
            'organizer' => null,
        ]);

        $response = $this->get(route('tournaments.index'));

        $response->assertStatus(200);
        $response->assertSee($tournament->getTitle());
    }

    public function test_index_shows_tournament_with_date_end(): void
    {
        $this->loginAdmin();

        $tournament = Tournament::factory()->create([
            'date' => Carbon::now()->addWeek(),
            'date_end' => Carbon::now()->addWeek()->addDays(2),
        ]);

        $response = $this->get(route('tournaments.index'));

        $response->assertStatus(200);
        $response->assertSee($tournament->getTitle());
    }

    public function test_index_shows_tournament_without_date_end(): void
    {
        $this->loginAdmin();

        $tournament = Tournament::factory()->create([
            'date' => Carbon::now()->addWeek(),
            'date_end' => null,
        ]);

        $response = $this->get(route('tournaments.index'));

        $response->assertStatus(200);
        $response->assertSee($tournament->getTitle());
    }

    public function test_show_displays_tournament_groups(): void
    {
        $this->loginAdmin();

        $tournament = Tournament::factory()->create();
        TournamentGroup::create([
            'tournament_id' => $tournament->getId(),
            'number' => 1,
            'name' => 'Юниоры 1',
            'registrations' => 12,
        ]);
        TournamentGroup::create([
            'tournament_id' => $tournament->getId(),
            'number' => 2,
            'name' => 'Юниоры 2',
            'registrations' => 15,
        ]);

        $response = $this->get(route('tournaments.show', $tournament->getId()));

        $response->assertStatus(200);
        $response->assertViewHas('groups');
        $response->assertSee('Юниоры 1');
        $response->assertSee('Юниоры 2');
        $response->assertSee('12');
        $response->assertSee('15');
    }

    public function test_show_groups_can_be_filtered_by_search(): void
    {
        $this->loginAdmin();

        $tournament = Tournament::factory()->create();
        TournamentGroup::create([
            'tournament_id' => $tournament->getId(),
            'number' => 1,
            'name' => 'Adults Standard',
            'registrations' => 10,
        ]);
        TournamentGroup::create([
            'tournament_id' => $tournament->getId(),
            'number' => 2,
            'name' => 'Youth Latin',
            'registrations' => 8,
        ]);

        $response = $this->get(route('tournaments.show', [
            'id' => $tournament->getId(),
            'search' => 'Standard',
        ]));

        $response->assertStatus(200);
        $groups = $response->viewData('groups');
        $this->assertCount(1, $groups);
        $this->assertEquals('Adults Standard', $groups->first()->getName());
    }

    public function test_show_groups_can_be_filtered_by_number(): void
    {
        $this->loginAdmin();

        $tournament = Tournament::factory()->create();
        TournamentGroup::create([
            'tournament_id' => $tournament->getId(),
            'number' => 1,
            'name' => 'Group A',
            'registrations' => 10,
        ]);
        TournamentGroup::create([
            'tournament_id' => $tournament->getId(),
            'number' => 2,
            'name' => 'Group B',
            'registrations' => 8,
        ]);

        $response = $this->get(route('tournaments.show', [
            'id' => $tournament->getId(),
            'number' => 1,
        ]));

        $response->assertStatus(200);
        $groups = $response->viewData('groups');
        $this->assertCount(1, $groups);
        $this->assertEquals(1, $groups->first()->getNumber());
    }

    public function test_show_groups_can_be_sorted(): void
    {
        $this->loginAdmin();

        $tournament = Tournament::factory()->create();
        TournamentGroup::create([
            'tournament_id' => $tournament->getId(),
            'number' => 2,
            'name' => 'Group B',
            'registrations' => 20,
        ]);
        TournamentGroup::create([
            'tournament_id' => $tournament->getId(),
            'number' => 1,
            'name' => 'Group A',
            'registrations' => 10,
        ]);

        $response = $this->get(route('tournaments.show', [
            'id' => $tournament->getId(),
            'sort_by' => 'name',
            'sort_order' => 'asc',
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('sortBy', 'name');
        $response->assertViewHas('sortOrder', 'asc');
        $groups = $response->viewData('groups');
        $this->assertEquals('Group A', $groups->first()->getName());
    }

    public function test_show_groups_pagination_works(): void
    {
        $this->loginAdmin();

        $tournament = Tournament::factory()->create();
        for ($i = 1; $i <= TournamentController::GROUPS_PER_PAGE + 2; $i++) {
            TournamentGroup::create([
                'tournament_id' => $tournament->getId(),
                'number' => $i,
                'name' => "Group {$i}",
                'registrations' => 10,
            ]);
        }

        $response = $this->get(route('tournaments.show', $tournament->getId()));

        $response->assertStatus(200);
        $groups = $response->viewData('groups');
        $this->assertInstanceOf(LengthAwarePaginator::class, $groups);
        $this->assertCount(TournamentController::GROUPS_PER_PAGE, $groups->items());
        $this->assertTrue($groups->hasMorePages());
    }

    public function test_show_groups_returns_empty_when_no_groups(): void
    {
        $this->loginAdmin();

        $tournament = Tournament::factory()->create();

        $response = $this->get(route('tournaments.show', $tournament->getId()));

        $response->assertStatus(200);
        $groups = $response->viewData('groups');
        $this->assertCount(0, $groups);
        $response->assertSee('Группы не найдены');
    }
}
