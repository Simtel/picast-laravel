<?php

declare(strict_types=1);

namespace App\Context\Youtube\Domain\Factory;

use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Domain\Model\VideoStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Video>
 */
class VideoFactory extends Factory
{
    /**
     * @var class-string<Video>
     */
    protected $model = Video::class;

    public function definition(): array
    {
        return [
            'url' => 'https://www.youtube.com/watch?v=BRCsU4D852M',
            'user_id' => User::all()->random()->id,
            'created_at' => now(),
            'updated_at' => now(),
            'title' => 'ЭТО САМЫЙ СЛОЖНЫЙ СКИЛЛ ТЕСТ? 5 ЧАСОВ ЖЕСТИ В ГТА 5 ОНЛАЙН',
            'thumb' => '',
            'status_id' => VideoStatus::where('code', '=', 'new')->firstOrFail()->id,
        ];
    }
}
