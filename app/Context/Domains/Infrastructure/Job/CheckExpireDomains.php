<?php

declare(strict_types=1);

namespace App\Context\Domains\Infrastructure\Job;

use App\Context\Domains\Domain\Model\Domain;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

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
        Domain::chunk(100, function ($domains) use ($now) {
            foreach ($domains as $domain) {
                try {
                    $expire_at = new Carbon($domain->expire_at);
                    $days = (int)abs($expire_at->diffInDays($now));

                    if (in_array($days, $this->days, true)) {
                        SendDomainExpireNotify::dispatch($domain);
                    }
                } catch (\Throwable $e) {
                    Log::error('CheckExpireDomains: ошибка обработки домена', [
                        'domain_id' => $domain->id,
                        'domain_name' => $domain->name,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        });
    }
}
