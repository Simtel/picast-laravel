<?php

namespace Tests\Unit;

use App\Events\DomainCreated;
use App\Listeners\GetWhoisDomain;
use App\Models\Domain;
use Event;
use Illuminate\Foundation\Testing\TestCase;
use Tests\CreatesApplication;

class ExampleTest extends TestCase
{
    use CreatesApplication;

    public function test_example(): void
    {
        Event::fake([DomainCreated::class]);
        Domain::factory(1)->create();

        $m = Domain::first();
        self::assertInstanceOf(Domain::class, $m);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->createApplication();
    }
}
