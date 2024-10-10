<?php

declare(strict_types=1);

namespace App\Context\Domains\Application\Contract;

use App\Context\Domains\Domain\Model\Domain;

interface WhoisUpdater
{
    /**
     * @param Domain $domain
     * @return void
     */
    public function update(Domain $domain): void;
}
