<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Domain\Factory;

use App\Context\Tournaments\Domain\Model\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Tournament>
 */
class TournamentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Tournament>
     */
    protected $model = Tournament::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $guid = fake()->uuid();
        $startDate = fake()->dateTimeBetween('+1 week', '+6 months');

        return [
            'title' => fake()->sentence(4),
            'link' => 'https://dancemanager.ru/competitions?guid=' . $guid,
            'date' => $startDate,
            'date_end' => fake()->optional(0.7)->dateTimeBetween($startDate, $startDate->format('Y-m-d H:i:s') . ' +3 days'),
            'city' => fake()->optional(0.8)->city(),
            'organizer' => fake()->optional(0.7)->company(),
            'guid' => $guid,
        ];
    }
}
