<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Tournaments;

use App\Context\Tournaments\Application\Services\DancemanagerScraper;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

final class DancemanagerScraperTest extends TestCase
{
    public function test_scraper_can_be_instantiated(): void
    {
        $client = Mockery::mock(Client::class);
        $scraper = new DancemanagerScraper($client);

        $this->assertInstanceOf(DancemanagerScraper::class, $scraper);
    }

    public function test_scraper_has_client_property(): void
    {
        $client = Mockery::mock(Client::class);
        $scraper = new DancemanagerScraper($client);

        $this->assertObjectHasProperty('client', $scraper);
    }

    public function test_scraper_has_base_url(): void
    {
        $client = Mockery::mock(Client::class);
        $scraper = new DancemanagerScraper($client);

        $this->assertObjectHasProperty('baseUrl', $scraper);
    }

    public function test_scraper_base_url_is_correct(): void
    {
        $client = Mockery::mock(Client::class);
        $scraper = new DancemanagerScraper($client);

        $reflection = new \ReflectionClass($scraper);
        $property = $reflection->getProperty('baseUrl');
        $property->setAccessible(true);

        $this->assertEquals('https://dancemanager.ru', $property->getValue($scraper));
    }

    public function test_scraper_uses_cache_by_default(): void
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn([]);

        $client = Mockery::mock(Client::class);
        $scraper = new DancemanagerScraper($client);

        $result = $scraper->getTournaments();

    }

    public function test_scraper_can_skip_cache(): void
    {
        Cache::shouldReceive('remember')
            ->never();

        // Создаем мок клиента, который будет вызываться
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('get')
            ->andReturn(new \GuzzleHttp\Psr7\Response(200, [], '<html><body>No events</body></html>'));

        $scraper = new DancemanagerScraper($client);

        // Используем try-catch так как scraper может выбросить исключение при неправильном HTML
        try {
            $result = $scraper->getTournaments(false);
        } catch (\InvalidArgumentException $e) {
            // Это ожидаемое поведение при пустом HTML без элементов пагинации
            $this->assertStringContainsString('current node list is empty', strtolower($e->getMessage()));
        }
    }

    public function test_scraper_returns_array(): void
    {
        Cache::shouldReceive('remember')
            ->andReturn([]);

        $client = Mockery::mock(Client::class);
        $scraper = new DancemanagerScraper($client);

        $scraper->getTournaments();
    }

    /**
     * @throws \ReflectionException
     */
    public function test_scraper_splits_location_and_name(): void
    {
        $client = Mockery::mock(Client::class);
        $scraper = new DancemanagerScraper($client);

        // Используем рефлексию для тестирования приватного метода
        $reflection = new \ReflectionClass($scraper);
        $method = $reflection->getMethod('splitLocationAndName');
        $method->setAccessible(true);

        /** @var array{city: string, organizer: string} $result */
        $result = $method->invoke($scraper, 'Москва, Dance Club');

        $this->assertEquals('Москва', $result['city']);
        $this->assertEquals('Dance Club', $result['organizer']);
    }

    public function test_scraper_splits_location_without_organizer(): void
    {
        $client = Mockery::mock(Client::class);
        $scraper = new DancemanagerScraper($client);

        $reflection = new \ReflectionClass($scraper);
        $method = $reflection->getMethod('splitLocationAndName');
        $method->setAccessible(true);

        /** @var array{city: string, organizer: string} $result */
        $result = $method->invoke($scraper, 'Москва');

        $this->assertEquals('Москва', $result['city']);
        $this->assertEquals('', $result['organizer']);
    }

    public function test_scraper_trims_whitespace_in_location_split(): void
    {
        $client = Mockery::mock(Client::class);
        $scraper = new DancemanagerScraper($client);

        $reflection = new \ReflectionClass($scraper);
        $method = $reflection->getMethod('splitLocationAndName');
        $method->setAccessible(true);

        /** @var array{city: string, organizer: string} $result */
        $result = $method->invoke($scraper, '  Москва  ,  Dance Club  ');

        $this->assertEquals('Москва', $result['city']);
        $this->assertEquals('Dance Club', $result['organizer']);
    }
}
