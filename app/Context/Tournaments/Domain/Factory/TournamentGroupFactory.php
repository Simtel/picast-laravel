<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Domain\Factory;

use App\Context\Tournaments\Domain\Model\TournamentGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TournamentGroup>
 */
class TournamentGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TournamentGroup>
     */
    protected $model = TournamentGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ageCategories = ['Юниоры', 'Молодежь', 'Взрослые', 'Сеньоры'];
        $styles = ['Стандарт', 'Латина', 'Европейская программа', 'Латиноамериканская программа'];
        $classes = ['E', 'D', 'C', 'B', 'A', 'S', 'M'];
        /** @var string $ageCategory */
        $ageCategory = fake()->randomElement($ageCategories);
        /** @var string $style */
        $style = fake()->randomElement($styles);
        /** @var string $class */
        $class = fake()->randomElement($classes);

        return [
            'tournament_id' => TournamentFactory::new(),
            'number' => fake()->numberBetween(1, 20),
            'name' => "{$ageCategory} {$style} {$class}",
            'registrations' => fake()->numberBetween(4, 30),
        ];
    }
}
