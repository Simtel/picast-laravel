<?php


namespace App\Facades;


use App\Contracts\SomeServiceContract;
use Illuminate\Support\Facades\Facade;

class SomeService extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return SomeServiceContract::class;
    }

}