<?php

declare(strict_types=1);

namespace Tests\Unit\Console\Commands;

use Illuminate\Testing\PendingCommand;
use Tests\TestCase;

final class ProcessQueueJobsCommandTest extends TestCase
{
    public function test_execute_command(): void
    {
        /** @var  PendingCommand $command */
        $command = $this->artisan('queue:process-jobs');

        $command->assertSuccessful()
            ->expectsOutput('Queue processing completed successfully');
    }
}
