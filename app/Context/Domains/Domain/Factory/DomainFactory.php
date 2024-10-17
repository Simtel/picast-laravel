<?php

declare(strict_types=1);

namespace App\Context\Domains\Domain\Factory;

use App\Context\Domains\Domain\Model\Domain;
use App\Context\User\Domain\Model\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Domain>
 */
class DomainFactory extends Factory
{
    /**
     * @var class-string<Domain>
     */
    protected $model = Domain::class;


    /**
     * @return array{name:string, user_id: int}
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->domainName(),
            'user_id' => User::all()->random()->id,
        ];
    }
}
