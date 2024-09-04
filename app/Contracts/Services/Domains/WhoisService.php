<?php

namespace App\Contracts\Services\Domains;

interface WhoisService
{
    /**
     * @return array<string, string>
     */
    public function getTimeFrameOptions(): array;

    public function deleteOldWhois(string $sub): int;
}
