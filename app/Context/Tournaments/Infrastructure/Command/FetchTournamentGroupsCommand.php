<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Infrastructure\Command;

use App\Context\Tournaments\Application\Services\TournamentGroupScrapper;
use App\Context\Tournaments\Domain\Model\Tournament;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Log;

class FetchTournamentGroupsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'tournaments:groups:fetch {tournament? : The Guid of the tournament}';

    /**
     * @var string
     */
    protected $description = 'Fetch groups for tournaments from Dancemanager and save to database';

    protected TournamentGroupScrapper $scraper;

    public function __construct(TournamentGroupScrapper $scraper)
    {
        parent::__construct();
        $this->scraper = $scraper;
        $this->scraper->setLogger(Log::channel('console'));
    }

    /**
     * @throws GuzzleException
     */
    public function handle(): void
    {
        $tournamentId = $this->argument('tournament');

        if (($tournamentId === null) && !$this->confirm('Continue to all tournaments?')) {
            return;
        }

        if ($tournamentId === null) {
            $this->fetchAllTournaments();
        }

        $tournament = Tournament::find($tournamentId);
        if ($tournament === null) {
            $this->error('Tournament not found');
            return;
        }

        $this->fetchForTournament($tournament);
    }

    /**
     * @throws GuzzleException
     */
    private function fetchAllTournaments(): void
    {
        $query = Tournament::query();
        $query->whereDate('date', '>', Carbon::now());
        $tournaments = $query->get();
        $this->info(sprintf('Getted %d tournaments from database', $tournaments->count()));

        foreach ($tournaments as $tournament) {
            $this->fetchForTournament($tournament);
        }
    }

    /**
     * @throws GuzzleException
     */
    private function fetchForTournament(Tournament $tournament): void
    {
        $this->info('Getting groups for ' . $tournament->getTitle());
        $this->scraper->getGroups($tournament);
    }
}
