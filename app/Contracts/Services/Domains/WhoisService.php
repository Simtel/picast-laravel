<?php


namespace App\Contracts\Services\Domains;


interface WhoisService
{

    public function getTimeFrameOptions(): array;

    public function deleteOldWhois(string $sub): int;
}