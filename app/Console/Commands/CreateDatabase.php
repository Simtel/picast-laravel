<?php

declare(strict_types=1);

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

final class CreateDatabase extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:create-database {name : The name of the database to create}';

    /**
     * @var string
     */
    protected $description = 'Create a new MySQL database with the given name.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $databaseName = $this->argument('name');

        if (!preg_match(pattern: '/^[a-zA-Z0-9_]+$/', subject: $databaseName)) {
            $this->error("Invalid database name: '$databaseName'");
            return;
        }

        try {
            DB::statement(sprintf('CREATE DATABASE IF NOT EXISTS %s', $databaseName));
            $this->info("Database $databaseName has been created successfully.");
        } catch (\Exception $e) {
            $this->error("Failed to create database: " . $e->getMessage());
        }
    }
}
