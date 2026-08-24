<?php

declare(strict_types=1);

namespace Tests\Unit\Context\Youtube;

use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Domain\Resource\VideoCollectionResource;
use App\Context\Youtube\Domain\Resource\VideoFullResource;
use App\Context\Youtube\Domain\Resource\VideoResource;
use Event;
use Illuminate\Http\Request;
use Tests\TestCase;

final class ResourceTest extends TestCase
{
    public function test_video_resource_to_array(): void
    {
        Event::fake();

        $video = Video::factory()->create();

        $resource = new VideoResource($video);
        $array = $resource->toArray(new Request());

        self::assertSame($video->getId(), $array['id']);
        self::assertSame($video->getUrl(), $array['url']);
    }

    public function test_video_full_resource_to_array(): void
    {
        Event::fake();

        $video = Video::factory()->create();

        $resource = new VideoFullResource($video);
        /** @var array<string, mixed> $array */
        $array = $resource->toArray(new Request());

        self::assertSame($video->getId(), $array['id']);
        self::assertSame($video->getTitle(), $array['title']);
        self::assertSame($video->getUrl(), $array['url']);
        self::assertSame($video->getCreatedAt()?->toDateTimeString(), $array['createdAt']);
        self::assertSame($video->getUpdatedAt()?->toDateTimeString(), $array['updatedAt']);
    }

    public function test_video_collection_resource_to_array(): void
    {
        Event::fake();

        $videos = Video::factory()->count(2)->create();

        $resource = new VideoCollectionResource($videos);
        $array = $resource->toArray(new Request());

        self::assertArrayHasKey('data', $array);
        self::assertCount(2, $array['data']);
    }
}
