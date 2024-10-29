<?php

declare(strict_types=1);

namespace App\Context\Youtube\Domain\Factory;

use App\Context\Youtube\Domain\Model\Video;
use App\Context\Youtube\Domain\Model\VideoFormats;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VideoFormats>
 */
class VideoFormatFactory extends Factory
{
    /**
     * @var class-string<VideoFormats>
     */
    protected $model = VideoFormats::class;

    public function definition(): array
    {
        return [
            'video_id'    => fn () => Video::factory()->create()->getId(),
            'format_id'   => $this->faker->randomNumber(2),
            'format_note' => 'some note for video',
            'format_ext'  => 'mp4',
            'vcodec'      => 'libx264',
            'resolution'  => '1280x720',
        ];
    }
}
