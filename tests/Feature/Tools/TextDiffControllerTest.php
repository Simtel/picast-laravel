<?php

declare(strict_types=1);

namespace Tests\Feature\Tools;

use Tests\TestCase;

final class TextDiffControllerTest extends TestCase
{
    public function test_text_diff_index_page(): void
    {
        $this->loginAdmin();

        $response = $this->get(route('tools.text-diff.index'));

        $response->assertStatus(200);
        $response->assertViewIs('personal.tools.text-diff.index');
        $response->assertSee('Сравнение текстов');
        $response->assertSee('text-diff-a');
        $response->assertSee('text-diff-b');
        $response->assertSee('text-diff-run');
    }

    public function test_text_diff_index_requires_auth(): void
    {
        $this->get(route('tools.text-diff.index'))->assertRedirect(route('login'));
    }

    public function test_text_diff_index_requires_view_tools_permission(): void
    {
        $user = $this->createUserWithPermissions([], []);

        $this->actingAs($user)->get(route('tools.text-diff.index'))->assertForbidden();
    }
}
