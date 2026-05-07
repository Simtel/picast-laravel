<?php

declare(strict_types=1);

namespace App\Context\Domains\Infrastructure\Command;

use App\Context\Domains\Domain\Model\Domain;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ListDomainsCommand extends Command
{
    protected $signature = 'domains:list {--user= : ID пользователя (по умолчанию: все домены)}';
    protected $description = 'Показать список всех доменов из базы данных';

    public function handle(): int
    {
        $userId = $this->option('user');

        $query = Domain::query()
            ->with(['user', 'whois'])
            ->orderBy('name');

        if ($userId) {
            $query->where('user_id', (int) $userId);
        }

        $domains = $query->get();

        if ($domains->isEmpty()) {
            $this->info('Домены не найдены.');

            return CommandAlias::SUCCESS;
        }

        $this->table(
            ['ID', 'Домен', 'Владелец', 'Дата создания', 'Истечение', 'Статус WHOIS'],
            $domains->map(static fn ($domain) => [
                $domain->id,
                $domain->name,
                $domain->user->name ?? 'N/A',
                $domain->created_at?->format('Y-m-d') ?? 'N/A',
                $domain->expire_at?->format('Y-m-d') ?? 'N/A',
                $domain->whois->isEmpty() ? 'Нет данных' : 'Доступен',
            ])->toArray()
        );

        $this->newLine();
        $this->info(sprintf('Всего доменов: %d', $domains->count()));

        return CommandAlias::SUCCESS;
    }
}
