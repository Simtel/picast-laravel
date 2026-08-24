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

        $user = $this->createUserWithPermissions([], ['edit youtube']);
        $token = $user->createToken('test')->plainTextToken;

        $video = Video::factory()->create(['user_id' => $user->id]);

        $response = $this->get(route('api.videos.index'), ['Authorization' => 'Bearer ' . $token]);

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

        $user = $this->createUserWithPermissions([], ['edit youtube']);
        $token = $user->createToken('test')->plainTextToken;

        $video = Video::factory()->create(['user_id' => $user->id]);

        $response = $this->get(
            route('api.videos.show', ['video' => $video]),
            ['Authorization' => 'Bearer ' . $token]
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

    public function test_user_can_create_video(): void
    {
        Event::fake();

        $user = $this->createUserWithPermissions([], ['edit youtube']);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->post(
            route('api.videos.store'),
            ['url' => 'https://www.youtube.com/watch?v=BRCsU4D852M'],
            ['Authorization' => 'Bearer ' . $token]
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas(
            Video::class,
            ['url' => 'https://www.youtube.com/watch?v=BRCsU4D852M', 'user_id' => $user->id]
        );
        $this->assertDatabaseCount(Video::class, 1);
    }

    public function test_user_can_update_video(): void
    {
        Event::fake();

        $user = $this->createUserWithPermissions([], ['edit youtube']);
        $token = $user->createToken('test')->plainTextToken;

        $video = Video::factory()->create(['user_id' => $user->id]);

        $response = $this->put(
            route('api.videos.update', ['video' => $video]),
            ['title' => 'Новый заголовок'],
            ['Authorization' => 'Bearer ' . $token]
        );

        $response->assertStatus(200);
    }

    public function test_user_can_delete_video(): void
    {
        Event::fake();

        $user = $this->createUserWithPermissions([], ['edit youtube']);
        $token = $user->createToken('test')->plainTextToken;

        $video = Video::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseCount(Video::class, 1);

        $response = $this->delete(
            route('api.videos.destroy', ['video' => $video]),
            [],
            ['Authorization' => 'Bearer ' . $token]
        );

        $response->assertStatus(200);
        $this->assertDatabaseCount(Video::class, 0);
    }
}
