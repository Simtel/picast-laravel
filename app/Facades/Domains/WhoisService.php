<?php


namespace App\Facades\Domains;


use Illuminate\Support\Facades\Facade;

class WhoisService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Contracts\Services\Domains\WhoisService::class;
    }

}