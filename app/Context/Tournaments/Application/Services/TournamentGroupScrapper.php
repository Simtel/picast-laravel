<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Application\Services;

use App\Context\Tournaments\Domain\DTO\TournamentGroupDto;
use App\Context\Tournaments\Domain\Model\Tournament;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\DomCrawler\Crawler;

class TournamentGroupScrapper
{
    private LoggerInterface $logger;

    /**
     * @param Client $client
     * @param LoggerInterface|null $logger
     */
    public function __construct(private readonly Client $client, ?LoggerInterface $logger)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }


    /**
     * @param Tournament $tournament
     * @return TournamentGroupDto[]
     * @throws GuzzleException
     */
    public function getGroups(Tournament $tournament): array
    {
        $response = $this->client->get($tournament->getLink());
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);


        $parts = $crawler->filter('a[data-partguid]');

        $this->logger->info('Найдено отделений:' . $parts->count());

        foreach ($parts as $part) {
            $partNode = new Crawler($part);
            $this->logger->info('Получение данных для '.trim($partNode->text()));
        }

        return [];
    }


}
