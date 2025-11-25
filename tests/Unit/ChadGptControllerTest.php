<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Context\ChadGPT\Infrastructure\Controller\ChadGptController;
use Tests\TestCase;

final class ChadGptControllerTest extends TestCase
{
    /**
     * Test that the ChadGPT controller can be instantiated
     */
    public function test_controller_can_be_instantiated(): void
    {
        $controller = new ChadGptController();
        $this->assertInstanceOf(ChadGptController::class, $controller);
    }
}
