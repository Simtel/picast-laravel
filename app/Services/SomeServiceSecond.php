<?php


namespace App\Services;


use App\Contracts\SomeServiceContract;

class SomeServiceSecond implements SomeServiceContract
{
    public $parameter;

    /**
     * SomeService constructor.
     * @param SomeParameter $parameter
     */
    public function __construct(SomeParameter $parameter)
    {
        $this->parameter = $parameter;
        $this->parameter->value = 20;
    }

    /**
     *
     */
    public function execute(): int
    {
        return $this->parameter->value;
    }


}