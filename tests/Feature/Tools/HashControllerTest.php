<?php

declare(strict_types=1);

namespace Tests\Feature\Tools;

use Tests\TestCase;

final class HashControllerTest extends TestCase
{
    public function test_hash_index_page(): void
    {
        $this->loginAdmin();

        $response = $this->get(route('tools.hash.index'));

        $response->assertStatus(200);
        $response->assertViewIs('personal.tools.hash.index');
        $response->assertSee('Хэш-генератор');
    }

    public function test_hash_computes_md5(): void
    {
        $this->loginAdmin();

        $response = $this->get(route('tools.hash.index', ['algorithm' => 'md5', 'text' => 'hello']));

        $response->assertStatus(200);
        $response->assertSee('5d41402abc4b2a76b9719d911017c592');
    }

    public function test_hash_computes_sha256(): void
    {
        $this->loginAdmin();

        $response = $this->get(route('tools.hash.index', ['algorithm' => 'sha256', 'text' => 'hello']));

        $response->assertStatus(200);
        $response->assertSee('2cf24dba5fb0a30e26e83b2ac5b9e29e1b161e5c1fa7425e73043362938b9824');
    }

    public function test_hash_requires_auth(): void
    {
        $this->get(route('tools.hash.index'))->assertRedirect(route('login'));
    }

    public function test_hash_rejects_unknown_algorithm(): void
    {
        $this->loginAdmin();

        $response = $this->from(route('tools.hash.index'))
            ->get(route('tools.hash.index', ['algorithm' => 'unknown', 'text' => 'x']));

        $response->assertRedirect(route('tools.hash.index'));
        $response->assertSessionHasErrors('algorithm');
    }
}
