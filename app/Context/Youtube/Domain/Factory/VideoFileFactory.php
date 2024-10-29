<?php

declare(strict_types=1);

namespace App\Context\Youtube\Domain\Factory;

use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Domain\Model\VideoFile;
use App\Context\Youtube\Domain\Model\VideoFormats;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VideoFile>
 */
class VideoFileFactory extends Factory
{
    /**
     * @var class-string<VideoFile>
     */
    protected $model = VideoFile::class;

    public function definition(): array
    {
        return [
            'video_id'    => fn () => Video::factory()->create()->getId(),
            'file_link' => fn () => 'file_name_'.$this->faker->randomNumber(3).'.mp4',
            'size' => fn () => $this->faker->randomNumber(5),
            'format_id' => fn () => VideoFormats::factory()->create()->getId(),
        ];
    }
}
