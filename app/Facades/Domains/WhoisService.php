<?php

declare(strict_types=1);

namespace App\Facades\Domains;

use Illuminate\Support\Facades\Facade;

class WhoisService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Contracts\Services\Domains\WhoisService::class;
    }
}
