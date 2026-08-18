<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Tournaments;

use App\Context\Tournaments\Domain\Model\Tournament;
use App\Context\Tournaments\Domain\Model\TournamentGroup;
use Tests\TestCase;

final class TournamentGroupTest extends TestCase
{
    public function test_group_has_all_fields(): void
    {
        $tournament = Tournament::factory()->create();
        $group = TournamentGroup::create([
            'tournament_id' => $tournament->getId(),
            'number' => 5,
            'name' => 'Юниоры Латина C',
            'registrations' => 17,
        ]);

        $this->assertSame($tournament->getId(), $group->getTournamentId());
        $this->assertEquals(5, $group->getNumber());
        $this->assertEquals('Юниоры Латина C', $group->getName());
        $this->assertEquals(17, $group->getRegistrations());
    }

    public function test_group_belongs_to_tournament(): void
    {
        $tournament = Tournament::factory()->create();
        $group = TournamentGroup::create([
            'tournament_id' => $tournament->getId(),
            'number' => 1,
            'name' => 'Group',
            'registrations' => 5,
        ]);

        $this->assertTrue($group->tournament()->getResults()->is($tournament));
    }

    public function test_group_casts_integers(): void
    {
        $tournament = Tournament::factory()->create();
        $group = TournamentGroup::create([
            'tournament_id' => $tournament->getId(),
            'number' => '3',
            'name' => 'Group',
            'registrations' => '9',
        ]);

        $this->assertSame(3, $group->getNumber());
        $this->assertSame(9, $group->getRegistrations());
        $this->assertSame($tournament->getId(), $group->getTournamentId());
    }
}
