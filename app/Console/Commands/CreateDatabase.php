<?php

declare(strict_types=1);

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class CreateDatabase extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:create-database {name}';

    /**
     * @var string
     */
    protected $description = 'Command description';

    public function handle(): void
    {
        $name = $this->argument('name');
        $this->info('Will be created  ' . $name . ' database');
        DB::statement(sprintf('CREATE DATABASE IF NOT EXISTS %s', $this->argument('name')));
        $this->info('Database successfully created!');
    }
}
