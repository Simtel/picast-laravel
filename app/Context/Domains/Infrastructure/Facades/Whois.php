<?php

declare(strict_types=1);

namespace App\Context\Domains\Infrastructure\Facades;

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
