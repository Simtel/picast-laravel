<?php

namespace App\Services\Domains;

use App\Facades\Whois;
use App\Models\Domain;
use App\Repositories\WhoisRepository;
use Carbon\Carbon;
use Telegram\Bot\Exceptions\TelegramSDKException;

readonly class WhoisUpdater implements \App\Contracts\Services\Domains\WhoisUpdater
{
    public function __construct(private readonly WhoisRepository $whoisRepository)
    {
    }

    /**
     * @param Domain $domain
     * @throws TelegramSDKException
     */
    public function update(Domain $domain): void
    {
        $lastWhois = $this->whoisRepository->getLastWhoisByDomain($domain);
        $whois = Whois::loadDomainInfo($domain->name);

        if ($lastWhois !== null && $lastWhois->isEqualResult($whois->getResponse()->text)) {
            return;
        }
        \App\Models\Whois::create(
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
