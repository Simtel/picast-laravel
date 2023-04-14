<?php

namespace Tests\Unit;

use Tests\CreatesApplication;

class ExampleTest extends \Illuminate\Foundation\Testing\TestCase
{
    use CreatesApplication;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        self::assertSame(true, true);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->createApplication();
    }
}
