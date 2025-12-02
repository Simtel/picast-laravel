<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Providers\BroadcastServiceProvider;
use Broadcast;
use Tests\TestCase;

class ProvidersTest extends TestCase
{
    public function test_boot_method_registers_routes_and_channels(): void
    {

        Broadcast::shouldReceive('routes')->once();
        Broadcast::shouldReceive('channel')->once();

        $basePath = base_path('routes/channels.php');
        $this->assertFileExists($basePath);

        $provider = new BroadcastServiceProvider($this->app);
        $provider->boot();

        $this->assertTrue(file_exists($basePath));
    }
}
