<?php


namespace App\Services\Domains;


use App\Models\Whois;

class WhoisService implements \App\Contracts\Services\Domains\WhoisService
{

    private array $time_frame_options = [
        'day' => '1 дня',
        'week' => '1 недели',
        'month' => '1 месяца',
    ];

    /**
     * @return array
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
        return Whois::whereDate('created_at', '<', $expire_at)->delete();
    }

}