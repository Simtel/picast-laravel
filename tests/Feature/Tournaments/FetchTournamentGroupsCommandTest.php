<?php

declare(strict_types=1);

namespace Tests\Feature\Tournaments;

use App\Context\Tournaments\Domain\Model\Tournament;
use App\Context\Tournaments\Domain\Model\TournamentGroup;
use App\Context\Tournaments\Infrastructure\Command\FetchTournamentGroupsCommand;
use Carbon\Carbon;
use Mockery;
use Simtel\DanceManagerScraper\TournamentGroupDto;
use Simtel\DanceManagerScraper\TournamentGroupScrapper;
use Tests\TestCase;

final class FetchTournamentGroupsCommandTest extends TestCase
{
    private function mockScraper(): \Mockery\LegacyMockInterface&\Mockery\MockInterface
    {
        $mock = Mockery::mock(TournamentGroupScrapper::class);
        $mock->shouldReceive('setLogger');
        $this->app->instance(TournamentGroupScrapper::class, $mock);

        return $mock;
    }

    public function test_command_is_registered(): void
    {
        $this->assertInstanceOf(FetchTournamentGroupsCommand::class, app(FetchTournamentGroupsCommand::class));
    }

    public function test_fetches_groups_for_specific_tournament(): void
    {
        /** @var Tournament $tournament */
        $tournament = Tournament::factory()->create();
        $scraper = $this->mockScraper();

        $scraper->shouldReceive('getGroups')
            ->once()
            ->andReturn([
                new TournamentGroupDto(1, 'Юниоры Стандарт C', 10),
                new TournamentGroupDto(2, 'Взрослые Латина S', 15),
            ]);

        $this->artisan('tournaments:groups:fetch', ['tournament' => (string)$tournament->getId()])
            ->expectsOutputToContain('Saved 2 groups for ' . $tournament->getTitle());

        $this->assertDatabaseHas('tournament_groups', [
            'tournament_id' => $tournament->getId(),
            'number' => 1,
            'name' => 'Юниоры Стандарт C',
            'registrations' => 10,
        ]);
        $this->assertDatabaseHas('tournament_groups', [
            'tournament_id' => $tournament->getId(),
            'number' => 2,
            'name' => 'Взрослые Латина S',
            'registrations' => 15,
        ]);
    }

    public function test_replaces_existing_groups(): void
    {
        /** @var Tournament $tournament */
        $tournament = Tournament::factory()->create();
        TournamentGroup::create([
            'tournament_id' => $tournament->getId(),
            'number' => 1,
            'name' => 'Old Group',
            'registrations' => 3,
        ]);

        $scraper = $this->mockScraper();
        $scraper->shouldReceive('getGroups')
            ->once()
            ->andReturn([
                new TournamentGroupDto(7, 'New Group', 20),
            ]);

        $this->artisan('tournaments:groups:fetch', ['tournament' => (string)$tournament->getId()]);

        $this->assertDatabaseMissing('tournament_groups', [
            'tournament_id' => $tournament->getId(),
            'name' => 'Old Group',
        ]);
        $this->assertDatabaseHas('tournament_groups', [
            'tournament_id' => $tournament->getId(),
            'number' => 7,
            'name' => 'New Group',
            'registrations' => 20,
        ]);
    }

    public function test_errors_when_tournament_not_found(): void
    {
        $scraper = $this->mockScraper();
        $scraper->shouldReceive('getGroups')->never();

        $this->artisan('tournaments:groups:fetch', ['tournament' => '999999'])
            ->expectsOutputToContain('Tournament not found');
    }

    public function test_aborts_when_user_declines(): void
    {
        Tournament::factory()->create();
        $scraper = $this->mockScraper();
        $scraper->shouldReceive('getGroups')->never();

        $this->artisan('tournaments:groups:fetch')
            ->expectsConfirmation('Continue to all tournaments?', 'no');

        $this->assertDatabaseCount('tournament_groups', 0);
    }

    public function test_fetches_groups_for_all_future_tournaments(): void
    {
        $tournament1 = Tournament::factory()->create(['date' => Carbon::now()->addWeek()]);
        $tournament2 = Tournament::factory()->create(['date' => Carbon::now()->addWeeks(2)]);
        Tournament::factory()->create(['date' => Carbon::now()->subWeek()]);

        $scraper = $this->mockScraper();
        $scraper->shouldReceive('getGroups')
            ->twice()
            ->andReturn([
                new TournamentGroupDto(1, 'Group A', 8),
            ]);

        $this->artisan('tournaments:groups:fetch')
            ->expectsConfirmation('Continue to all tournaments?', 'yes');

        $this->assertDatabaseHas('tournament_groups', [
            'tournament_id' => $tournament1->getId(),
            'name' => 'Group A',
        ]);
        $this->assertDatabaseHas('tournament_groups', [
            'tournament_id' => $tournament2->getId(),
            'name' => 'Group A',
        ]);
    }
}
