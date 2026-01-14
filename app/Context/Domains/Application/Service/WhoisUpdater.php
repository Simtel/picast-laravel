<?php

declare(strict_types=1);

namespace App\Context\Domains\Application\Service;

use App\Context\Domains\Domain\Model\Domain;
use App\Context\Domains\Infrastructure\Facades\Whois;
use Illuminate\Support\Carbon;
use Override;
use Telegram\Bot\Exceptions\TelegramSDKException;

final readonly class WhoisUpdater implements \App\Context\Domains\Application\Contract\WhoisUpdater
{
    /**
     * @param Domain $domain
     * @throws TelegramSDKException
     */
    #[Override]
    public function update(Domain $domain): void
    {
        $whois = Whois::loadDomainInfo($domain->name);
        \App\Context\Domains\Domain\Model\Whois::create(
            [
                'domain_id' => $domain->id,
                'text' => $whois->getResponse()->text,
            ]
        );
        $domain->expire_at = Carbon::createFromTimestamp($whois->expirationDate);
        $domain->updated_at = Carbon::now();
        $domain->owner = $whois->owner;
        $domain->save();
    }
}
