<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Context\Domains\Domain\Event\DomainCreated;
use App\Context\Domains\Domain\Model\Domain;
use DB;
use Event;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Notification;
use Tests\CreatesApplication;
use Tests\TestCase;
use Throwable;

final class ExampleTest extends TestCase
{
    use CreatesApplication;
    use DatabaseTransactions;

    /**
     * @throws Throwable
     */
    public function test_example(): void
    {
        DB::beginTransaction();
        Notification::fake();
        Event::fake([DomainCreated::class]);
        Domain::factory(1)->create();

        $m = Domain::first();
        self::assertInstanceOf(Domain::class, $m);
        DB::rollback();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->createApplication();
    }
}
