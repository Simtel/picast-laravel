<?php

namespace App\Console\Commands;

use App\Facades\Whois;
use App\Models\Domain;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Iodev\Whois\Factory;

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
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $domains = Domain::all();
        foreach ($domains as $domain) {
            $this->output->writeln('Обработка домена:'.$domain->name);
            $whois = Whois::loadDomainInfo($domain->name);
            \App\Models\Whois::create(
                [
                    'domain_id' => $domain->id,
                    'text' => $whois->getResponse()->text,
                ]
            );
            $domain->expire_at = Carbon::createFromTimestamp($whois->getExpirationDate());
            $domain->owner = $whois->getOwner();
            $domain->save();

        }
        $this->output->success('Закончили обновление');
    }
}
