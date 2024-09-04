<?php

namespace App\Contracts\Services\Domains;

use App\Models\Domains\Domain;

interface WhoisUpdater
{
    /**
     * @param Domain $domain
     * @return void
     */
    public function update(Domain $domain): void;
}
