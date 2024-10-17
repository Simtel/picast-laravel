<?php

declare(strict_types=1);

namespace App\Context\Domains\Application\Service;

use App\Context\Domains\Domain\Model\Whois;
use Illuminate\Database\Query\Builder;

class WhoisService implements \App\Context\Domains\Application\Contract\WhoisService
{
    /**
     * @var array<string, string>
     */
    private array $time_frame_options = [
        'day' => '1 дня',
        'week' => '1 недели',
        'month' => '1 месяца',
    ];

    /**
     * @return array<string>
     */
    public function getTimeFrameOptions(): array
    {
        return $this->time_frame_options;
    }

    /**
     * @param string $sub example day,week,month
     */
    public function deleteOldWhois(string $sub): int
    {
        $expire_at = now()->sub('1 ' . $sub);

        /** @var Builder $builder */
        $builder = Whois::whereDate('created_at', '<', $expire_at);
        return $builder->delete();
    }
}
