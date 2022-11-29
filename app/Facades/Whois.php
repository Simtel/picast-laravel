<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Whois extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'whois';
    }
}
