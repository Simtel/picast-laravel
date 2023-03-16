<?php

namespace App\Services\Domains;

use App\Facades\Whois;
use App\Models\Domain;
use Carbon\Carbon;

class WhoisUpdater implements \App\Contracts\Services\Domains\WhoisUpdater
{
    /**
     * @param Domain $domain
     */
    public function update(Domain $domain): void
    {
        $whois = Whois::loadDomainInfo($domain->name);
        \App\Models\Whois::create(
            [
                'domain_id' => $domain->id,
                'text' => $whois->getResponse()->text,
            ]
        );
        $domain->expire_at = Carbon::createFromTimestamp($whois->expirationDate);
        $domain->owner = $whois->owner;
        $domain->save();

        \Telegram::setAsyncRequest(true)->sendMessage(
            ['chat_id' => env('TELEGRAM_MAIN_CHANNEL'), 'text' => 'Update info for domain:' . $domain->name]
        );
    }
}
