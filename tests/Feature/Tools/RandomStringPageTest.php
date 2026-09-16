<?php

declare(strict_types=1);

namespace Tests\Feature\Tools;

use Tests\TestCase;

final class RandomStringPageTest extends TestCase
{
    public function test_random_string_page_opens(): void
    {
        $this->loginAdmin();

        $response = $this->get(route('tools.random-string.index'));

        $response->assertOk()->assertSee('Генератор случайных строк');
    }

    public function test_random_string_page_generates_on_submit(): void
    {
        $this->loginAdmin();

        $response = $this->get(route('tools.random-string.index', [
            'length' => 24,
            'uppercase' => 1,
            'lowercase' => 1,
            'digits' => 1,
            'symbols' => 0,
        ]));

        $response->assertOk()->assertSee('Генератор случайных строк');
    }
}