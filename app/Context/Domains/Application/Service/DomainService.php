<?php

declare(strict_types=1);

namespace App\Context\Domains\Application\Service;

use App\Context\Domains\Application\Contract\WhoisUpdater;
use App\Context\Domains\Domain\Model\Domain;
use Illuminate\Support\Facades\Log;

final class DomainService
{
    public function __construct(
        private readonly WhoisUpdater $whoisUpdater,
    ) {
    }

    /**
     * @param array{name: string} $data
     */
    public function create(int $userId, array $data): Domain
    {
        $domain = Domain::create(
            [
                'name' => $data['name'],
                'user_id' => $userId,
            ]
        );

        Log::info('[DomainService.create] домен создан', [
            'name' => $domain->getName(),
            'user' => $userId,
        ]);

        return $domain;
    }

    public function delete(Domain $domain): void
    {
        $domain->delete();

        Log::info('[DomainService.delete] домен удалён', ['name' => $domain->getName()]);
    }

    public function updateWhois(Domain $domain): void
    {
        Log::debug('[DomainService.updateWhois] обновление whois', ['name' => $domain->getName()]);

        $this->whoisUpdater->update($domain);
    }
}
