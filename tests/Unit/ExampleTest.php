<?php

namespace Tests\Unit;

use App\Models\Domain;
use PHPUnit\Framework\TestCase;
use Tests\CreatesApplication;

class ExampleTest extends TestCase
{

    use CreatesApplication;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {

        $m = Domain::first();
        self::assertInstanceOf(Domain::clas);


    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->createApplication();
    }
}
