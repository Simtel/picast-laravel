<?php

declare(strict_types=1);

namespace Tests\Feature\Tools;

use Tests\TestCase;

final class TimestampControllerTest extends TestCase
{
    public function test_timestamp_index_page(): void
    {
        $this->loginAdmin();

        $response = $this->get(route('tools.timestamp.index'));

        $response->assertStatus(200);
        $response->assertViewIs('personal.tools.timestamp.index');
        $response->assertSee('Конвертер времени');
        $response->assertSee('Timestamp → дата');
        $response->assertSee('Дата → timestamp');
    }

    public function test_timestamp_index_requires_auth(): void
    {
        $this->get(route('tools.timestamp.index'))->assertRedirect(route('login'));
    }

    public function test_timestamp_index_requires_view_tools_permission(): void
    {
        $user = $this->createUserWithPermissions([], []);

        $this->actingAs($user)->get(route('tools.timestamp.index'))->assertForbidden();
    }
}
