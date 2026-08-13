<?php

declare(strict_types=1);

namespace Tests\Feature\Tournaments;

use App\Context\Tournaments\Domain\Model\Tournament;
use App\Context\Tournaments\Domain\Model\TournamentGroup;
use App\Context\Tournaments\Infrastructure\Job\DeleteExpiredTournaments;
use Tests\TestCase;

final class DeleteExpiredTournamentsTest extends TestCase
{
    private function createWithDate(?string $date, ?string $dateEnd, string $guid, bool $withGroup = true): Tournament
    {
        /** @var Tournament $tournament */
        $tournament = Tournament::factory()->create([
            'title' => 'Tournament ' . $guid,
            'link' => 'https://example.com/' . $guid,
            'guid' => $guid,
            'date' => $date,
            'date_end' => $dateEnd,
        ]);

        if ($withGroup) {
            TournamentGroup::create([
                'tournament_id' => $tournament->getId(),
                'number' => 1,
                'name' => 'Group 1',
                'registrations' => 5,
            ]);
        }

        return $tournament;
    }

    public function test_deletes_past_tournaments_with_groups(): void
    {
        $past = $this->createWithDate(now()->subDays(10)->toDateString(), now()->subDays(8)->toDateString(), 'past-guid');

        (new DeleteExpiredTournaments())->handle();

        $this->assertDatabaseMissing('tournaments', ['id' => $past->getId()]);
        $this->assertDatabaseMissing('tournament_groups', ['tournament_id' => $past->getId()]);
    }

    public function test_deletes_past_tournament_without_end_date(): void
    {
        $past = $this->createWithDate(now()->subDays(10)->toDateString(), null, 'past-no-end-guid');

        (new DeleteExpiredTournaments())->handle();

        $this->assertDatabaseMissing('tournaments', ['id' => $past->getId()]);
    }

    public function test_keeps_future_tournaments(): void
    {
        $future = $this->createWithDate(now()->addDays(10)->toDateString(), now()->addDays(12)->toDateString(), 'future-guid');

        (new DeleteExpiredTournaments())->handle();

        $this->assertDatabaseHas('tournaments', ['id' => $future->getId()]);
    }
}
