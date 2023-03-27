<?php

namespace App\Console\Commands;

use App\Contracts\Services\Domains\WhoisUpdater;
use App\Models\Domain;
use Illuminate\Console\Command;

class DomainsWhois extends Command
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
        $domains = Domain::all();
        foreach ($domains as $domain) {
            $this->output->writeln('Обработка домена:' . $domain->name);
            $this->whoisUpdater->update($domain);
        }
        $this->output->success('Закончили обновление');
    }
}
