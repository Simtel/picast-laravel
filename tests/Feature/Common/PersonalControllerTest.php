<?php

declare(strict_types=1);

namespace Tests\Feature\Common;

use Tests\TestCase;

class PersonalControllerTest extends TestCase
{
    public function test_personal_index_page_admin(): void
    {
        $this->loginAdmin();

        $response = $this->get('/personal');
        $response->assertStatus(200);
        $response->assertViewIs('personal.index');
        $response->assertSee('Пользователи');
    }

    public function test_personal_index_page_user(): void
    {
        $this->authUserWithPermissions([], ['domains']);

        $response = $this->get('/personal');
        $response->assertStatus(302);
        $response->assertRedirect(route('domains.index'));
    }
}
