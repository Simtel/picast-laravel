<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Infrastructure\Job;

use App\Context\Tournaments\Infrastructure\Service\ExpiredTournamentsCleaner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteExpiredTournaments implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ExpiredTournamentsCleaner $cleaner): void
    {
        $cleaner->clean();
    }
}
