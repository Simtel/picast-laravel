<?php

namespace App\Jobs;

use App\Models\Domain;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckExpireDomains implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * За сколько дней отправлять уведомление
     * @var array|int[]
     */
    private array $days = [1, 3, 7, 30];

    private Carbon $now;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->now = Carbon::now();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $domains = Domain::all();
        foreach ($domains as $domain) {
            $expire_at = new Carbon($domain->expire_at);
            $days = $expire_at->diffInDays($this->now);
            if (in_array($days, $this->days, true)) {
                SendDomainExpireNotify::dispatch($domain);
            }
        }
    }
}
