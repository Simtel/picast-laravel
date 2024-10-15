<?php

declare(strict_types=1);

namespace YouTube;

use Alaouy\Youtube\Facades\Youtube;
use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Infrastructure\Jobs\UpdateVideoFormats;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Queue;
use stdClass;
use Tests\TestCase;

class YoutubeControllerTest extends TestCase
{
    /**
     * @dataProvider dataProviderForValidationTest
     * @param mixed $url
     * @param string|string[] $error
     * @return void
     */
    public function test_validate_youtube_link(mixed $url, string|array $error): void
    {
        /** @var User $user */
        $user = User::find(1);
        Auth::login($user);

        $data = [
            'url' => $url,
        ];

        $response = $this->post(route('youtube.store'), $data);
        $response->assertStatus(302);
        $response->assertInvalid(['url' => $error]);
    }

    /**
     * @return array<int,array{url:int|string, error: string|string[]}>
     */
    public static function dataProviderForValidationTest(): array
    {
        return [
            [
                'url'  => 'youtube',
                'error' => 'Поле url должно содержать валидную ссылку на видео YouTube.'
            ],
            [
                'url'  => 1,
                'error' => [
                    'Поле url должно быть строкой.',
                    'Поле url должно содержать валидную ссылку на видео YouTube.'
                ]
            ]
        ];
    }

    public function test_user_can_add_youtube_url(): void
    {
        /** @var User $user */
        $user = User::find(1);
        Auth::login($user);

        Queue::fake();

        $stdClass = new \StdClass();
        $stdClass->snippet = new StdClass();
        $stdClass->snippet->title = 'Тестовый заголовок';

        Youtube::shouldReceive('getVideoInfo')
            ->once()
            ->with('BRCsU4D852M')
            ->andReturn($stdClass);

        Youtube::shouldReceive('parseVidFromURL')
            ->once()
            ->with('https://www.youtube.com/watch?v=BRCsU4D852M')
            ->andReturn('BRCsU4D852M');

        $data = [
            'url' => 'https://www.youtube.com/watch?v=BRCsU4D852M',
        ];

        $response = $this->post(route('youtube.store'), $data);
        $response->assertStatus(302);

        Queue::assertPushed(UpdateVideoFormats::class, 1);

        $this->assertDatabaseHas(Video::class, ['url' => 'https://www.youtube.com/watch?v=BRCsU4D852M']);
    }
}
