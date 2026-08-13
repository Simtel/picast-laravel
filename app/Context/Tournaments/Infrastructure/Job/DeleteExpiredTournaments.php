<?php

declare(strict_types=1);

namespace App\Context\Tournaments\Infrastructure\Job;

use App\Context\Tournaments\Domain\Model\Tournament;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class DeleteExpiredTournaments implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Tournament::query()
            ->where(static function ($query) {
                $query->where('date_end', '<', now())
                    ->orWhere(static function ($query) {
                        $query->whereNull('date_end')
                            ->where('date', '<', now());
                    });
            })
            ->chunkById(100, static function ($tournaments) {
                foreach ($tournaments as $tournament) {
                    try {
                        DB::transaction(static function () use ($tournament) {
                            $tournament->groups()->delete();
                            $tournament->delete();
                        });
                    } catch (\Throwable $e) {
                        Log::error('DeleteExpiredTournaments: ошибка удаления турнира', [
                            'tournament_id' => $tournament->getId(),
                            'title' => $tournament->getTitle(),
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            });
    }
}
