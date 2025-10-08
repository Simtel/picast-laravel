<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Controllers\Personal\ChadGpt\ChadGptController;
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

    /**
     * Test that the index method returns a view
     */
    public function test_index_returns_view(): void
    {
        $this->loginAdmin();
        $response = $this->get(route('chadgpt.index'));

        $response->assertStatus(200);
        $response->assertViewIs('personal.chadgpt.index');
    }
}
