<?php

declare(strict_types=1);

namespace Tests\Feature\YouTube;

use App\Context\Youtube\Domain\Model\Video;
use Event;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ApiVideoControllerTest extends TestCase
{
    public function test_user_can_see_videos(): void
    {
        Event::fake();

        $user = $this->createUserWithPermissions(['api_token' => 123], ['edit youtube']);

        $video = Video::factory()->create(['user_id' => $user->id]);

        $response = $this->get(route('api.videos.index', ), ['Authorization' => 'Bearer ' . $user->api_token]);

        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) => $json->whereType('data', 'array')
        );

        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data')
                ->has(
                    'data.0',
                    fn (AssertableJson $json) => $json->where('id', $video->getId())
                        ->where('url', $video->getUrl())
                        ->etc()
                )
        );
    }
}
