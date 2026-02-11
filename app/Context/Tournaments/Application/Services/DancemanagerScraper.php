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
     * @return list<array{title: string, date: mixed, date_end: mixed, link: non-falsy-string}>
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
     * @return list<array{title: string, date: mixed, date_end: mixed, link: non-falsy-string}>
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
            $dates = $this->extractDatesFromCompetitionPage($link);

            $tournaments[] = [
                'title' => $title,
                'date' => $dates['start'] ?? 'N/A',
                'date_end' => $dates['end'] ?? null,
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

                        // Extract dates from the competition page
                        $dates = $this->extractDatesFromCompetitionPage($link);

                        $tournaments[] = [
                            'title' => $title,
                            'date' => $dates['start'] ?? 'N/A',
                            'date_end' => $dates['end'] ?? null,
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

    /**
     * @return array{start: string|null, end: string|null}
     */
    private function extractDatesFromCompetitionPage(string $url): array
    {
        try {
            $response = $this->client->get($url);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            // Look for date information in the page content
            $content = $crawler->filter('body')->text();

            // Russian months map
            $monthMap = [
                'января' => '01', 'февраля' => '02', 'марта' => '03', 'апреля' => '04',
                'мая' => '05', 'июня' => '06', 'июля' => '07', 'августа' => '08',
                'сентября' => '09', 'октября' => '10', 'ноября' => '11', 'декабря' => '12',
            ];

            // Look for two dates pattern: DD.MM.YYYY<br>DD.MM.YYYY
            $twoDatesPattern = '/\b(0?[1-9]|[12][0-9]|3[01])[\.\/\-](0?[1-9]|1[0-2])[\.\/\-](\d{4})\s*<br>\s*(0?[1-9]|[12][0-9]|3[01])[\.\/\-](0?[1-9]|1[0-2])[\.\/\-](\d{4})\b/i';
            if (preg_match($twoDatesPattern, $content, $matches)) {
                return [
                    'start' => sprintf('%02d.%02d.%s', (int)$matches[1], (int)$matches[2], $matches[3]),
                    'end' => sprintf('%02d.%02d.%s', (int)$matches[4], (int)$matches[5], $matches[6]),
                ];
            }

            // Look for single date pattern: DD.MM.YYYY
            $dmyPattern = '/\b(0?[1-9]|[12][0-9]|3[01])[\.\/\-](0?[1-9]|1[0-2])[\.\/\-](\d{4})\b/';
            if (preg_match($dmyPattern, $content, $matches)) {
                $date = sprintf('%02d.%02d.%s', (int)$matches[1], (int)$matches[2], $matches[3]);
                return ['start' => $date, 'end' => null];
            }

            // Look for Russian date pattern: DD month YYYY
            $dayMonthYearPattern = '/\b(0?[1-9]|[12][0-9]|3[01])\s+(января|февраля|марта|апреля|мая|июня|июля|августа|сентября|октября|ноября|декабря)\s+(\d{4})\b/i';
            if (preg_match($dayMonthYearPattern, $content, $matches)) {
                $month = $monthMap[mb_strtolower($matches[2])] ?? '01';
                $date = sprintf('%02d.%02d.%s', (int)$matches[1], (int)$month, $matches[3]);
                return ['start' => $date, 'end' => null];
            }

            // If no specific date found, return null
            return ['start' => null, 'end' => null];
        } catch (\Exception $e) {
            // If there's an error fetching the competition page, return null
            return ['start' => null, 'end' => null];
        }
    }
}
