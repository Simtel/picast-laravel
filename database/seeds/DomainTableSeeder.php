<?php

namespace Database\Seeders;

use App\Models\Domains\Domain;
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
