<?php

declare(strict_types=1);

namespace Tests\Feature\Tools;

use Tests\TestCase;

final class ColorControllerTest extends TestCase
{
    public function test_color_index_page(): void
    {
        $this->loginAdmin();

        $response = $this->get(route('tools.color.index'));

        $response->assertStatus(200);
        $response->assertViewIs('personal.tools.color.index');
        $response->assertSee('Конвертер цветов');
        $response->assertSee('color-picker');
        $response->assertSee('#3366cc');
        $response->assertSee('rgb(51, 102, 204)');
        $response->assertSee('hsl(220, 60%, 50%)');
        $response->assertSee('Нет точного совпадения');
    }

    public function test_color_index_requires_auth(): void
    {
        $this->get(route('tools.color.index'))->assertRedirect(route('login'));
    }

    public function test_color_index_requires_view_tools_permission(): void
    {
        $user = $this->createUserWithPermissions([], []);

        $this->actingAs($user)->get(route('tools.color.index'))->assertForbidden();
    }
}
