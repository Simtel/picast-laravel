<?php

declare(strict_types=1);

namespace Tests\Feature\Tools;

use Tests\TestCase;

final class JsonDiffControllerTest extends TestCase
{
    public function test_json_diff_index_page(): void
    {
        $this->loginAdmin();

        $response = $this->get(route('tools.json-diff.index'));

        $response->assertStatus(200);
        $response->assertViewIs('personal.tools.json-diff.index');
        $response->assertSee('Сравнение JSON');
        $response->assertSee('json-diff-a');
        $response->assertSee('json-diff-b');
        $response->assertSee('json-diff-run');
    }

    public function test_json_diff_index_requires_auth(): void
    {
        $this->get(route('tools.json-diff.index'))->assertRedirect(route('login'));
    }

    public function test_json_diff_index_requires_view_tools_permission(): void
    {
        $user = $this->createUserWithPermissions([], []);

        $this->actingAs($user)->get(route('tools.json-diff.index'))->assertForbidden();
    }
}
