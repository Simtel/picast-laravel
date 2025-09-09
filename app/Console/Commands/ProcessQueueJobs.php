<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class ProcessQueueJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:process-jobs 
                           {--queue=default : The queue to process}
                           {--timeout=60 : The timeout for each job}
                           {--memory=128 : The memory limit in MB}
                           {--tries=3 : Number of attempts for each job}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process queue jobs with specified parameters';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $queue = $this->option('queue');
        $timeout = $this->option('timeout');
        $memory = $this->option('memory');
        $tries = $this->option('tries');

        $this->info("Starting queue worker for queue: {$queue}");

        try {
            // Запуск обработчика очереди
            Artisan::call('queue:work', [
                '--queue' => $queue,
                '--timeout' => $timeout,
                '--memory' => $memory,
                '--tries' => $tries,
                '--once' => true, // Обработать только одну задачу
            ]);

            $this->info('Queue processing completed successfully');

        } catch (\Exception $e) {
            $this->error('Error processing queue: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
