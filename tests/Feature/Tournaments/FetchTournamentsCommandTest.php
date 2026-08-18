<?php

declare(strict_types=1);

namespace Tests\Feature\Tournaments;

use App\Context\Tournaments\Domain\Model\Tournament;
use App\Context\Tournaments\Infrastructure\Command\FetchTournamentsCommand;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use Mockery;
use Simtel\DanceManagerScraper\DancemanagerScraper;
use Simtel\DanceManagerScraper\TournamentDto;
use Tests\TestCase;

final class FetchTournamentsCommandTest extends TestCase
{
    private function mockScraper(): \Mockery\LegacyMockInterface&\Mockery\MockInterface
    {
        $mock = Mockery::mock(DancemanagerScraper::class);
        $this->app->instance(DancemanagerScraper::class, $mock);

        return $mock;
    }

    /**
     * @return TournamentDto
     */
    private function dto(string $title, string $link, string $date = '2026-06-01'): TournamentDto
    {
        return TournamentDto::fromArray([
            'title' => $title,
            'date' => $date,
            'date_end' => null,
            'link' => $link,
            'city' => 'Москва',
            'organizer' => 'Club',
        ]);
    }

    public function test_command_is_registered(): void
    {
        $this->assertInstanceOf(FetchTournamentsCommand::class, app(FetchTournamentsCommand::class));
    }

    public function test_saves_fetched_tournaments(): void
    {
        $scraper = $this->mockScraper();

        $scraper->shouldReceive('getTournaments')->once()->andReturn([
            $this->dto('Cup 1', 'https://dancemanager.ru/competitions?guid=guid-1'),
            $this->dto('Cup 2', 'https://dancemanager.ru/competitions?guid=guid-2', 'N/A'),
        ]);

        $this->artisan('tournaments:fetch')
            ->expectsOutputToContain('Загрузили 2 турниров...')
            ->expectsOutputToContain('Tournaments fetched and saved successfully.');

        $this->assertDatabaseHas('tournaments', ['guid' => 'guid-1', 'title' => 'Cup 1']);
        $this->assertDatabaseHas('tournaments', ['guid' => 'guid-2', 'title' => 'Cup 2']);
        $this->assertDatabaseMissing('tournaments', ['guid' => 'guid-2', 'date' => 'N/A']);

        $this->assertDatabaseHas('tournaments', [
            'guid' => 'guid-1',
            'city' => 'Москва',
            'organizer' => 'Club',
        ]);
    }

    public function test_skips_tournament_with_empty_guid(): void
    {
        $scraper = $this->mockScraper();

        $scraper->shouldReceive('getTournaments')->once()->andReturn([
            $this->dto('No Guid Cup', 'https://dancemanager.ru/competitions?guid='),
        ]);

        $this->artisan('tournaments:fetch')->expectsOutputToContain('Tournaments fetched and saved successfully.');

        $this->assertDatabaseMissing('tournaments', ['title' => 'No Guid Cup']);
    }

    public function test_updates_existing_tournament_by_guid(): void
    {
        $scraper = $this->mockScraper();

        $existing = Tournament::factory()->create([
            'guid' => 'guid-update',
            'title' => 'Old Title',
            'link' => 'https://dancemanager.ru/competitions?guid=guid-update',
        ]);

        $scraper->shouldReceive('getTournaments')->once()->andReturn([
            $this->dto('New Title', 'https://dancemanager.ru/competitions?guid=guid-update'),
        ]);

        $this->artisan('tournaments:fetch');

        $this->assertDatabaseHas('tournaments', [
            'id' => $existing->getId(),
            'guid' => 'guid-update',
            'title' => 'New Title',
        ]);
    }

    public function test_handles_guzzle_exception(): void
    {
        $scraper = $this->mockScraper();

        $scraper->shouldReceive('getTournaments')->once()->andThrow(
            new ConnectException('Network is down', new Request('GET', 'https://dancemanager.ru'))
        );

        $this->artisan('tournaments:fetch')
            ->expectsOutputToContain('Network is down')
            ->assertExitCode(0);
    }

    public function test_handles_generic_exception_when_guid_missing(): void
    {
        $scraper = $this->mockScraper();

        $scraper->shouldReceive('getTournaments')->once()->andReturn([
            $this->dto('Broken Cup', 'https://dancemanager.ru'),
        ]);

        $this->artisan('tournaments:fetch')
            ->expectsOutputToContain('Error fetching tournaments')
            ->assertExitCode(0);
    }
}
