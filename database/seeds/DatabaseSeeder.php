<?php

declare(strict_types=1);


use Database\Seeders\YouTubeVideoStatusSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(YouTubeVideoStatusSeeder::class);
    }
}
