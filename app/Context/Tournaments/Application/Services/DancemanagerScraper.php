<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Application\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Cache;

class DancemanagerScraper
{
    protected Client $client;
    protected string $baseUrl = 'https://dancemanager.ru';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param bool $useCache
     * @return list<array{title: string, date: mixed, link: non-falsy-string}>
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTournaments(bool $useCache = true): array
    {
        if ($useCache) {
            return Cache::remember('dancemanager_tournaments', now()->addHours(1), function () {
                return $this->fetchTournaments();
            });
        }

        return $this->fetchTournaments();
    }

    /**
     * @return list<array{title: string, date: mixed, link: non-falsy-string}>
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function fetchTournaments(): array
    {
        $tournaments = [];

        // First, get the main page
        $url = $this->baseUrl;
        $response = $this->client->get($url);
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        // Extract tournaments from the current page
        $events = $crawler->filter('div[id^="event_"]');

        foreach ($events as $eventDiv) {
            $eventNode = new Crawler($eventDiv);
            $eventId = $eventNode->attr('id');
            $guid = str_replace('event_', '', $eventId);

            $title = trim($eventNode->text());
            $link = $this->baseUrl . '/competitions?guid=' . $guid;

            // Since the main page doesn't show dates, we need to extract them from the competition page
            $date = $this->extractDateFromCompetitionPage($link);

            $tournaments[] = [
                'title' => $title,
                'date' => $date,
                'link' => $link,
            ];
        }

        // Handle pagination - check if there's a next page
        $currentPage = $crawler->filter('li.page-item.active a.page-link')->text();
        $nextPageExists = $crawler->filter('li.page-item a.page-link:contains("»")')->count() > 0;

        if ($nextPageExists) {
            // Get the href of the next page link
            $nextPageElement = $crawler->filter('li.page-item a.page-link:contains("»")')->first();
            $nextPageHref = $nextPageElement->attr('href');

            // Extract page parameter from the href
            if (preg_match('/page(\d+)=([2-9]\d*)/', $nextPageHref, $matches)) {
                $pageNum = $matches[2];
                $pageParam = $matches[1];

                // Continue paginating
                while ($pageNum <= 10) { // Limit to 10 pages to avoid infinite loops
                    $paginatedUrl = $this->baseUrl . '/?page' . $pageParam . '=' . $pageNum;

                    $response = $this->client->get($paginatedUrl);
                    $html = $response->getBody()->getContents();
                    $crawler = new Crawler($html);

                    $events = $crawler->filter('div[id^="event_"]');

                    if ($events->count() === 0) {
                        break; // No more events
                    }

                    foreach ($events as $eventDiv) {
                        $eventNode = new Crawler($eventDiv);
                        $eventId = $eventNode->attr('id');
                        $guid = str_replace('event_', '', $eventId);

                        $title = trim($eventNode->text());
                        $link = $this->baseUrl . '/competitions?guid=' . $guid;

                        // Extract date from the competition page
                        $date = $this->extractDateFromCompetitionPage($link);

                        $tournaments[] = [
                            'title' => $title,
                            'date' => $date,
                            'link' => $link,
                        ];
                    }

                    // Check if there's another page
                    $nextPageExists = $crawler->filter('li.page-item a.page-link:contains("»")')->count() > 0;
                    if (!$nextPageExists) {
                        break;
                    }

                    $pageNum++;
                }
            }
        }

        // Remove duplicates based on GUID
        $uniqueTournaments = [];
        $seenGuids = [];

        foreach ($tournaments as $tournament) {
            $guid = basename(parse_url($tournament['link'], PHP_URL_QUERY));
            $guid = str_replace('guid=', '', $guid);

            if (!isset($seenGuids[$guid])) {
                $seenGuids[$guid] = true;
                $uniqueTournaments[] = $tournament;
            }
        }

        // Sort by date if available, otherwise by title
        usort($uniqueTournaments, static function ($a, $b) {
            if ($a['date'] !== 'N/A' && $b['date'] !== 'N/A') {
                return strtotime($a['date']) - strtotime($b['date']);
            }

            if ($a['date'] !== 'N/A') {
                return -1; // Put items with dates first
            }

            if ($b['date'] !== 'N/A') {
                return 1; // Put items with dates first
            }

            return strcmp($a['title'], $b['title']); // Sort by title if no dates
        });

        return $uniqueTournaments;
    }

    private function extractDateFromCompetitionPage(string $url): string
    {
        try {
            $response = $this->client->get($url);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            // Look for date information in the page content
            $content = $crawler->filter('body')->text();

            // Look for Russian date patterns
            // Pattern for day month year format (e.g., 15 февраля 2026)
            $dayMonthYearPattern = '/\b(0?[1-9]|[12][0-9]|3[01])\s+(января|февраля|марта|апреля|мая|июня|июля|августа|сентября|октября|ноября|декабря)\s+\d{4}\b/i';
            if (preg_match($dayMonthYearPattern, $content, $matches)) {
                return trim($matches[0]);
            }

            // Pattern for month year format (e.g., февраль 2026)
            $monthYearPattern = '/\b(января|февраля|марта|апреля|мая|июня|июля|августа|сентября|октября|ноября|декабря)\s+\d{4}\b/i';
            if (preg_match($monthYearPattern, $content, $matches)) {
                return trim($matches[0]);
            }

            // Pattern for day.month.year format (e.g., 15.02.2026)
            $dmyPattern = '/\b(0?[1-9]|[12][0-9]|3[01])[\.\/\-](0?[1-9]|1[0-2])[\.\/\-]\d{2,4}\b/';
            if (preg_match($dmyPattern, $content, $matches)) {
                return trim($matches[0]);
            }

            // If no specific date found, return 'N/A'
            return 'N/A';
        } catch (\Exception $e) {
            // If there's an error fetching the competition page, return 'N/A'
            return 'N/A';
        }
    }
}
