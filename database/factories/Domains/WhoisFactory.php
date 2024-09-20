<?php

declare(strict_types=1);

namespace Database\Factories\Domains;

use App\Models\Domains\Whois;
use Illuminate\Database\Eloquent\Factories\Factory;

class WhoisFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Whois::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
        ];
    }
}
