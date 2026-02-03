<?php

declare(strict_types=1);

namespace App\Context\Webcams\Infrastructure\Factory;

use App\Context\Webcams\Domain\Model\Webcam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Webcam>
 */
class WebcamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->city . ' Webcam',
            'location' => $this->faker->address,
            'stream_url' => $this->faker->url,
            'preview_url' => $this->faker->imageUrl(640, 480, 'city'),
            'description' => $this->faker->sentence,
            'is_active' => $this->faker->boolean(90),
        ];
    }

    /**
     * Indicate that the webcam is active.
     */
    public function active(): static
    {
        return $this->state(static fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the webcam is inactive.
     */
    public function inactive(): static
    {
        return $this->state(static fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
