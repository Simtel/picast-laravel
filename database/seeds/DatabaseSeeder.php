<?php


use Database\Seeders\DomainTableSeeder;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(DomainTableSeeder::class);
    }
}
