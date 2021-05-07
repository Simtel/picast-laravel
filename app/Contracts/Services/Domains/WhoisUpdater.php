<?php


namespace App\Contracts\Services\Domains;


use App\Models\Domain;

interface WhoisUpdater
{
    public function update(Domain $domain);
}