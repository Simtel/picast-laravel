<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Context\Domains\Domain\Model\Domain;
use Illuminate\Database\Seeder;

class DomainTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Domain::factory()->count(50)->hasUsers(1)->create();
    }
}
