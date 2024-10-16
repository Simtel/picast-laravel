<?php

declare(strict_types=1);

namespace YouTube;

use Alaouy\Youtube\Facades\Youtube;
use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Infrastructure\Jobs\UpdateVideoFormats;
use App\Context\Youtube\Infrastructure\Repository\YouTubeVideoStatusRepository;
use App\Models\User;
use Auth;
use Illuminate\Contracts\Container\BindingResolutionException;
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
                'url'   => 'youtube',
                'error' => 'Поле url должно содержать валидную ссылку на видео YouTube.'
            ],
            [
                'url'   => 1,
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

        $this->mockGetVideoInfo('BRCsU4D852M', 'Тестовый заголовок 222');
        $this->mockParseVidFromUrl('https://www.youtube.com/watch?v=BRCsU4D852M', 'BRCsU4D852M');

        $data = [
            'url' => 'https://www.youtube.com/watch?v=BRCsU4D852M',
        ];

        $response = $this->post(route('youtube.store'), $data);
        $response->assertStatus(302);

        Queue::assertPushed(UpdateVideoFormats::class, 1);

        $this->assertDatabaseHas(Video::class, ['url' => 'https://www.youtube.com/watch?v=BRCsU4D852M']);
        $this->assertDatabaseCount(Video::class, 1);
    }


    /**
     * @throws BindingResolutionException
     */
    public function test_user_can_see_added_video(): void
    {
        $this->loginAdmin();

        Queue::fake();

        $this->mockGetVideoInfo('BRCsU4D852M', 'Тестовый заголовок 222');
        $this->mockParseVidFromUrl('https://www.youtube.com/watch?v=BRCsU4D852M', 'BRCsU4D852M');

        $statusRepository = $this->app->make(YouTubeVideoStatusRepository::class);

        $video = Video::create(
            [
                'url'       => 'https://www.youtube.com/watch?v=BRCsU4D852M',
                'user_id'   => \Illuminate\Support\Facades\Auth::id(),
                'status_id' => $statusRepository->findByCode('new')->id,
            ]
        );

        Queue::assertPushed(UpdateVideoFormats::class, 1);

        $this->assertDatabaseCount(Video::class, 1);

        $view = $this->view('personal.youtube_videos.index', ['videos' => [$video]]);
        $view->assertSee('Тестовый заголовок 222');

        $response = $this->get(route('youtube.index'));
        $response->assertStatus(200);
        $response->assertViewHas('videos');
        $response->assertSee('Тестовый заголовок 222');
    }

    private function mockGetVideoInfo(string $videoId, string $title = 'Тестовый заголовок'): void
    {
        $stdClass = new \StdClass();
        $stdClass->snippet = new StdClass();
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
