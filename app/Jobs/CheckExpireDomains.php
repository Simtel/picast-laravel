<?php

namespace App\Jobs;

use App\Models\Domains\Domain;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckExpireDomains implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * За сколько дней отправлять уведомление
     * @var int[]
     */
    private array $days = [1, 3, 7, 30];



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
    public function handle(): void
    {
        $now = Carbon::now();
        foreach ($this->getDomains() as $domain) {
            $expire_at = new Carbon($domain->expire_at);
            $days = (int)abs($expire_at->diffInDays($now));

            if (in_array($days, $this->days, true)) {
                SendDomainExpireNotify::dispatch($domain);
            }
        }
    }

    /**
     * @return Collection<int, Domain>
     */
    protected function getDomains(): Collection
    {
        return Domain::all();
    }
}
