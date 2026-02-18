<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Tournaments;

use App\Context\Tournaments\Domain\Model\Tournament;
use Tests\TestCase;

final class TournamentTest extends TestCase
{
    public function test_tournament_has_required_fields(): void
    {
        $tournament = Tournament::factory()->create([
            'title' => 'Test Tournament',
            'link' => 'https://example.com/tournament',
            'guid' => 'test-guid-123',
        ]);

        $this->assertEquals('Test Tournament', $tournament->getTitle());
        $this->assertEquals('https://example.com/tournament', $tournament->getLink());
        $this->assertEquals('test-guid-123', $tournament->getGuid());
    }

    public function test_tournament_has_dates(): void
    {
        $startDate = now()->addWeek();
        $endDate = now()->addWeek()->addDays(2);

        $tournament = Tournament::factory()->create([
            'date' => $startDate,
            'date_end' => $endDate,
        ]);

        // Сравниваем только дату, так как в БД поле date имеет тип varchar
        $this->assertEquals($startDate->format('Y-m-d'), $tournament->getDate()?->format('Y-m-d'));
        $this->assertEquals($endDate->format('Y-m-d'), $tournament->getDateEnd()?->format('Y-m-d'));
    }

    public function test_tournament_has_optional_city(): void
    {
        $tournament = Tournament::factory()->create([
            'city' => 'Москва',
        ]);

        $this->assertEquals('Москва', $tournament->getCity());
    }

    public function test_tournament_city_can_be_null(): void
    {
        $tournament = Tournament::factory()->create([
            'city' => null,
        ]);

        $this->assertNull($tournament->getCity());
    }

    public function test_tournament_has_optional_organizer(): void
    {
        $tournament = Tournament::factory()->create([
            'organizer' => 'Dance Club',
        ]);

        $this->assertEquals('Dance Club', $tournament->getOrganizer());
    }

    public function test_tournament_organizer_can_be_null(): void
    {
        $tournament = Tournament::factory()->create([
            'organizer' => null,
        ]);

        $this->assertNull($tournament->getOrganizer());
    }

    public function test_tournament_has_timestamps(): void
    {
        $tournament = Tournament::factory()->create();

        $this->assertNotNull($tournament->getCreatedAt());
        $this->assertNotNull($tournament->getUpdatedAt());
    }

    public function test_tournament_date_end_can_be_null(): void
    {
        $tournament = Tournament::factory()->create([
            'date' => now()->addWeek(),
            'date_end' => null,
        ]);

        $this->assertNotNull($tournament->getDate());
        $this->assertNull($tournament->getDateEnd());
    }
}
