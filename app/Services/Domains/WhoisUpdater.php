<?php

declare(strict_types=1);

namespace App\Services\Domains;

use App\Facades\Whois;
use App\Models\Domains\Domain;
use Illuminate\Support\Carbon;
use Telegram\Bot\Exceptions\TelegramSDKException;

readonly class WhoisUpdater implements \App\Contracts\Services\Domains\WhoisUpdater
{
    /**
     * @param Domain $domain
     * @throws TelegramSDKException
     */
    public function update(Domain $domain): void
    {
        $whois = Whois::loadDomainInfo($domain->name);
        \App\Models\Domains\Whois::create(
            [
                'domain_id' => $domain->id,
                'text' => $whois->getResponse()->text,
            ]
        );
        $domain->expire_at = Carbon::createFromTimestamp($whois->expirationDate);
        $domain->owner = $whois->owner;
        $domain->save();
    }
}
