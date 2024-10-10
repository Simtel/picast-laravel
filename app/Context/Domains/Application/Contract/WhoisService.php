<?php

declare(strict_types=1);

namespace App\Context\Domains\Application\Contract;

interface WhoisService
{
    /**
     * @return array<string, string>
     */
    public function getTimeFrameOptions(): array;

    public function deleteOldWhois(string $sub): int;
}
