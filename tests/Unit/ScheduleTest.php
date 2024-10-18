<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Support\Facades\Schedule;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    public function test_schedule_list(): void
    {
        // $schedule = resolve(Schedule::class);
        $events = Schedule::events();

        $expected = [
            "'/usr/local/bin/php' 'artisan' domains:whois" => '0 0 * * *',
            "'/usr/local/bin/php' 'artisan' youtube:download" => '* * * * *',
            'App\Context\Domains\Infrastructure\Job\CheckExpireDomains' => '0 0 * * *',
        ];

        $actual = [];

        foreach ($events as $event) {
            $actual[$event->description ?? $event->command] = $event->getExpression();
        }
        $this->assertEquals(sort($expected), sort($actual));
    }
}
