<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Infrastructure\Command;

use App\Context\Tournaments\Application\Services\DancemanagerScraper;
use App\Context\Tournaments\Domain\Model\Tournament;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

// Предполагается, что модель Tournament находится в корневом пространстве имен App

class FetchTournamentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tournaments:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch tournaments from Dancemanager and save to database';

    protected DancemanagerScraper $scraper;

    public function __construct(DancemanagerScraper $scraper)
    {
        parent::__construct();
        $this->scraper = $scraper;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Fetching tournaments...');
        try {
            $tournaments = $this->scraper->getTournaments(false);

            foreach ($tournaments as $tournamentData) {
                $linkParts = parse_url($tournamentData['link']);
                parse_str($linkParts['query'] ?? '', $query);
                $guid = $query['guid'] ?? null;

                if (!$guid) {
                    Log::warning('Skipping tournament due to missing GUID: ' . json_encode($tournamentData));
                    continue;
                }

                Tournament::updateOrCreate(
                    ['guid' => $guid],
                    [
                        'title' => $tournamentData['title'],
                        'link' => $tournamentData['link'],
                        'date' => $tournamentData['date'],
                    ]
                );
            }
            $this->info('Tournaments fetched and saved successfully.');
        } catch (\Exception $e) {
            Log::error('Error fetching tournaments: ' . $e->getMessage());
            $this->error('Error fetching tournaments: ' . $e->getMessage());
        }
    }
}
