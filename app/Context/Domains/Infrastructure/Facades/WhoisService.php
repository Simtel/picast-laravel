<?php

declare(strict_types=1);

namespace App\Context\Domains\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

final class WhoisService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Context\Domains\Application\Contract\WhoisService::class;
    }
}
