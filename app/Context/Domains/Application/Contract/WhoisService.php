<?php

declare(strict_types=1);

namespace App\Context\Domains\Application\Contract;

use App\Context\Domains\Domain\Model\Domain;

interface WhoisService
{
    /**
     * @return array<string, string>
     */
    public function getTimeFrameOptions(): array;

    public function deleteOldWhois(Domain $domain, string $sub): int;
}
