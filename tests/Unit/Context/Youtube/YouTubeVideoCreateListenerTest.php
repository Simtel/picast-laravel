<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Youtube;

use Alaouy\Youtube\Facades\Youtube;
use App\Context\Youtube\Application\Listener\YouTubeVideoCreateListener;
use App\Context\Youtube\Domain\Event\YouTubeVideoCreated;
use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Domain\Observer\YouTubeVideoObserver;
use App\Context\Youtube\Infrastructure\Jobs\UpdateVideoFormats;
use Event;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Queue;
use stdClass;
use Tests\TestCase;

final class YouTubeVideoCreateListenerTest extends TestCase
{
    /**
     * @throws BindingResolutionException
     */
    public function test_handle_updates_title_from_string(): void
    {
        Event::fake();
        Queue::fake();

        $video = Video::factory()->create(['url' => 'https://www.youtube.com/watch?v=BRCsU4D852M']);

        $this->mockGetVideoInfo('BRCsU4D852M', 'Тестовый заголовок');
        $this->mockParseVidFromUrl('https://www.youtube.com/watch?v=BRCsU4D852M', 'BRCsU4D852M');

        $listener = new YouTubeVideoCreateListener();
        $listener->handle(new YouTubeVideoCreated($video));

        self::assertEquals('Тестовый заголовок', $video->title);
        Queue::assertPushed(UpdateVideoFormats::class, 1);
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_handle_updates_title_from_non_string(): void
    {
        Event::fake();
        Queue::fake();

        $video = Video::factory()->create(['url' => 'https://www.youtube.com/watch?v=BRCsU4D852M']);

        $this->mockGetVideoInfo('BRCsU4D852M', 12345);
        $this->mockParseVidFromUrl('https://www.youtube.com/watch?v=BRCsU4D852M', 'BRCsU4D852M');

        $listener = new YouTubeVideoCreateListener();
        $listener->handle(new YouTubeVideoCreated($video));

        self::assertEquals('12345', $video->title);
    }

    public function test_observer_dispatches_created_event(): void
    {
        Event::fake();

        $video = Video::factory()->create();

        $observer = new YouTubeVideoObserver();
        $observer->created($video);

        Event::assertDispatched(YouTubeVideoCreated::class, 1);
    }

    private function mockGetVideoInfo(string $videoId, mixed $title): void
    {
        $stdClass = new stdClass();
        $stdClass->snippet = new stdClass();
        $stdClass->snippet->title = $title;

        Youtube::shouldReceive('getVideoInfo')
            ->once()
            ->with($videoId)
            ->andReturn($stdClass);
    }

    private function mockParseVidFromUrl(string $url, string $videoId): void
    {
        Youtube::shouldReceive('parseVidFromURL')
            ->once()
            ->with($url)
            ->andReturn($videoId);
    }
}
