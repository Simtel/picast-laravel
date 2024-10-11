<?php

declare(strict_types=1);

namespace App\Context\Domains\Domain\Factory;

use App\Context\Domains\Domain\Model\Domain;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Domain>
 */
class DomainFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Domain>
     */
    protected $model = Domain::class;


    /**
     * Define the model's default state.
     *
     * @return array{name:string, user_id: int}
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'user_id' => User::all()->random()->id,
        ];
    }
}
