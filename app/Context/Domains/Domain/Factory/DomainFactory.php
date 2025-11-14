<?php

declare(strict_types=1);

namespace App\Context\Domains\Domain\Factory;

use App\Context\Domains\Domain\Model\Domain;
use App\Context\User\Domain\Model\User;
use Closure;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Domain>
 */
final class DomainFactory extends Factory
{
    /**
     * @var class-string<Domain>
     */
    protected $model = Domain::class;


    /**
     * @return array{name:string, user_id: Closure}
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->domainName(),
            'user_id' => static fn () => User::all()->random()->id,
        ];
    }
}
