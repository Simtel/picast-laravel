<?php

declare(strict_types=1);

namespace Tests\Feature\YouTube;

use App\Context\Youtube\Domain\Model\Video;
use Event;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

final class ApiVideoControllerTest extends TestCase
{
    public function test_user_can_see_videos(): void
    {
        Event::fake();

        $user = $this->createUserWithPermissions(['api_token' => 123], ['edit youtube']);

        $video = Video::factory()->create(['user_id' => $user->id]);

        $response = $this->get(route('api.videos.index', ), ['Authorization' => 'Bearer ' . $user->api_token]);

        $response->assertStatus(200);

        $response->assertJson(
            static fn (AssertableJson $json) => $json->whereType('data', 'array')
        );

        $response->assertJson(
            static fn (AssertableJson $json) => $json->has('data')
                ->has(
                    'data.0',
                    static fn (AssertableJson $json) => $json->where('id', $video->getId())
                        ->where('url', $video->getUrl())
                        ->etc()
                )
        );
    }

    public function test_user_can_see_video_info(): void
    {
        Event::fake();

        $user = $this->createUserWithPermissions(['api_token' => 123], ['edit youtube']);

        $video = Video::factory()->create(['user_id' => $user->id]);

        $response = $this->get(
            route('api.videos.show', ['video' => $video]),
            ['Authorization' => 'Bearer ' . $user->api_token]
        );

        $response->assertStatus(200);

        $response->assertJson(
            static fn (AssertableJson $json) => $json->whereType('data', 'array')
        );

        $response->assertJson(
            static fn (AssertableJson $json) => $json->has(
                'data',
                static fn (AssertableJson $json) => $json->where('id', $video->getId())
                    ->where('url', $video->getUrl())
                    ->where('title', $video->getTitle())
                    ->where('createdAt', $video->getCreatedAt()?->format('Y-m-d H:i:s'))
                    ->where('updatedAt', $video->getUpdatedAt()?->format('Y-m-d H:i:s'))
                    ->etc()
            )
        );
    }
}
