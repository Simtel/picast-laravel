<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Infrastructure\Command;

use App\Context\Tournaments\Domain\Model\Tournament;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Simtel\DanceManagerScraper\DancemanagerScraper;

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
            try {
                $tournaments = $this->scraper->getTournaments();
            } catch (GuzzleException $e) {
                $this->error($e->getMessage());
                return;
            }

            $this->info('Загрузили ' . count($tournaments) . ' турниров...');
            foreach ($tournaments as $tournament) {
                $linkParts = parse_url($tournament->getLink());
                parse_str($linkParts['query'] ?? '', $query);
                $guid = $query['guid'] ?? null;

                if (!$guid) {
                    Log::warning(
                        'Skipping tournament due to missing GUID: ' . json_encode(
                            $tournament->getGuid(),
                            JSON_THROW_ON_ERROR
                        )
                    );
                    continue;
                }

                Tournament::updateOrCreate(
                    ['guid' => $guid],
                    [
                        'title' => $tournament->getTitle(),
                        'link' => $tournament->getLink(),
                        'date' => $tournament->getDate() !== 'N/A' ? $tournament->getDate() : null,
                        'date_end' => $tournament->getDateEnd(),
                        'city' => $tournament->getCity(),
                        'organizer' => $tournament->getOrganizer(),
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
