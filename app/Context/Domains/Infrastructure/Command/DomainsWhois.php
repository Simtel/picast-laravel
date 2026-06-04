<?php

declare(strict_types=1);

namespace App\Context\Domains\Infrastructure\Command;

use App\Context\Domains\Application\Contract\WhoisUpdater;
use App\Context\Domains\Domain\Model\Domain;
use Illuminate\Console\Command;
use Log;

final class DomainsWhois extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:whois';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all domains whois';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(private readonly WhoisUpdater $whoisUpdater)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        Domain::chunk(100, function ($domains) {
            foreach ($domains as $domain) {
                try {
                    $this->output->writeln('Обработка домена:' . $domain->name);
                    $this->whoisUpdater->update($domain);
                } catch (\Throwable $e) {
                    Log::error('DomainsWhois: ошибка обновления WHOIS', [
                        'domain_id' => $domain->id,
                        'domain_name' => $domain->name,
                        'error' => $e->getMessage(),
                    ]);
                    $this->output->error('Ошибка для ' . $domain->name . ': ' . $e->getMessage());
                }
            }
        });
        $this->output->success('Закончили обновление');
    }
}
