<?php

declare(strict_types=1);

namespace Tests\Feature\Command;

use Illuminate\Testing\PendingCommand;
use Tests\TestCase;

class CreateDataBaseCommandTest extends TestCase
{
    public function test_create_database_command(): void
    {
        /** @var  PendingCommand $command */
        $command = $this->artisan('app:create-database test');

        $command->assertSuccessful()
            ->expectsOutput('Will be created  test database')
            ->expectsOutput('Database successfully created!');
    }
}
