<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Domain;
use App\Models\Whois;

class WhoisRepository
{
    public function getLastWhoisByDomain(
        Domain $domain
    ): ?Whois {
        return Whois::where('domain_id', '=', $domain->id)->orderBy('created_at', 'desc')->first();
    }
}
