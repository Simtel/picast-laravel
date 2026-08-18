<?php

declare(strict_types=1);

namespace Tests\Feature\Tournaments;

use App\Context\Tournaments\Domain\Model\Tournament;
use App\Context\Tournaments\Domain\Model\TournamentGroup;
use App\Context\Tournaments\Infrastructure\Service\ExpiredTournamentsCleaner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facade;
use Tests\TestCase;

final class ExpiredTournamentsCleanerTest extends TestCase
{
    public function test_clean_continues_when_deletion_throws(): void
    {
        $past = Tournament::factory()->create([
            'date' => now()->subDays(10)->toDateString(),
            'date_end' => now()->subDays(5)->toDateString(),
        ]);
        TournamentGroup::create([
            'tournament_id' => $past->getId(),
            'number' => 1,
            'name' => 'Group 1',
            'registrations' => 5,
        ]);
        $future = Tournament::factory()->create([
            'date' => now()->addDays(10)->toDateString(),
        ]);

        DB::shouldReceive('transaction')->andThrow(new \RuntimeException('DB is down'));

        $result = app(ExpiredTournamentsCleaner::class)->clean();

        DB::clearResolvedInstance('db');
        Facade::clearResolvedInstance('db');

        $this->assertEquals(['tournaments' => 0, 'groups' => 0], $result);
        $this->assertNotNull(Tournament::find($past->getId()));
        $this->assertNotNull(Tournament::find($future->getId()));
    }
}
