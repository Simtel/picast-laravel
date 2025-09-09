<?php

declare(strict_types=1);

namespace Tests\Feature\Command;

use Illuminate\Testing\PendingCommand;
use Tests\TestCase;

final class CreateDataBaseCommandTest extends TestCase
{
    public function test_create_database_command(): void
    {
        /** @var  PendingCommand $command */
        $command = $this->artisan('app:create-database test');

        $command->assertSuccessful()
            ->expectsOutput('Database test has been created successfully.');
    }
}
