<?php

declare(strict_types=1);

namespace App\Context\Domains\Infrastructure\Command;

use App\Context\Domains\Domain\Model\Domain;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ListDomainsCommand extends Command
{
    private const array TABLE_HEADERS = [
        'ID',
        'Домен',
        'Владелец',
        'Дата создания',
        'Истечение',
        'Статус WHOIS',
    ];

    private const string DATE_FORMAT = 'Y-m-d';
    private const string NOT_AVAILABLE = 'N/A';
    private const string WHOIS_MISSING = 'Нет данных';
    private const string WHOIS_AVAILABLE = 'Доступен';

    protected $signature = 'domains:list {--user= : ID пользователя (по умолчанию: все домены)}';
    protected $description = 'Показать список всех доменов из базы данных';

    public function handle(): int
    {
        $domains = $this->fetchDomains($this->resolveUserId());

        if ($domains->isEmpty()) {
            $this->info('Домены не найдены.');

            return CommandAlias::SUCCESS;
        }

        $this->renderDomainsTable($domains);
        $this->renderTotal($domains->count());

        return CommandAlias::SUCCESS;
    }

    private function resolveUserId(): ?int
    {
        $userId = $this->option('user');

        return $userId !== null ? (int) $userId : null;
    }

    /**
     * @param int|null $userId
     * @return Collection<int, Domain>
     */
    private function fetchDomains(?int $userId): Collection
    {
        return Domain::query()
            ->with(['user', 'whois'])
            ->orderBy('name')
            ->when($userId, static fn (Builder $query, int $id) => $query->where('user_id', $id))
            ->get();
    }

    /**
     * @param Collection<int, Domain> $domains
     * @return void
     */
    private function renderDomainsTable(Collection $domains): void
    {
        $this->table(
            self::TABLE_HEADERS,
            $domains->map(fn (Domain $domain) => $this->mapDomainToRow($domain))->toArray()
        );
    }

    /**
     * @param Domain $domain
     * @return array{
     *      0: int|string,
     *      1: string,
     *      2: string,
     *      3: string,
     *      4: string,
     *      5: string
     *  }
     */
    private function mapDomainToRow(Domain $domain): array
    {
        return [
            $domain->id,
            $domain->name,
            $domain->user->name ?? self::NOT_AVAILABLE,
            $domain->created_at?->format(self::DATE_FORMAT) ?? self::NOT_AVAILABLE,
            $domain->expire_at?->format(self::DATE_FORMAT) ?? self::NOT_AVAILABLE,
            $domain->whois->isEmpty() ? self::WHOIS_MISSING : self::WHOIS_AVAILABLE,
        ];
    }

    private function renderTotal(int $count): void
    {
        $this->newLine();
        $this->info(sprintf('Всего доменов: %d', $count));
    }
}
