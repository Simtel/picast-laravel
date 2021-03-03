<?php


namespace App\Services;


use App\Contracts\SomeServiceContract;

class SomeService implements SomeServiceContract
{
    public $parameter;

    /**
     * SomeService constructor.
     * @param SomeParameter $parameter
     */
    public function __construct(SomeParameter $parameter)
    {
        $this->parameter = $parameter;
    }

    /**
     *
     */
    public function execute(): int
    {
       return $this->parameter->value;
    }


}