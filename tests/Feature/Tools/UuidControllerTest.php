<?php

declare(strict_types=1);

namespace Tests\Feature\Tools;

use Tests\TestCase;

final class UuidControllerTest extends TestCase
{
    public function test_uuid_index_page(): void
    {
        $this->loginAdmin();

        $response = $this->get(route('tools.uuid.index'));

        $response->assertStatus(200);
        $response->assertViewIs('personal.tools.uuid.index');
        $response->assertSee('UUID-генератор');
    }

    public function test_uuid_generates_v4(): void
    {
        $this->loginAdmin();

        $response = $this->get(route('tools.uuid.index', ['version' => 'v4', 'count' => 1]));

        $response->assertStatus(200);
        $result = $response->viewData('result');
        self::assertIsArray($result);
        self::assertCount(1, $result);
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $result[0],
        );
    }

    public function test_uuid_generates_v7(): void
    {
        $this->loginAdmin();

        $response = $this->get(route('tools.uuid.index', ['version' => 'v7', 'count' => 1]));

        $response->assertStatus(200);
        $result = $response->viewData('result');
        self::assertIsArray($result);
        self::assertCount(1, $result);
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $result[0],
        );
    }

    public function test_uuid_requires_auth(): void
    {
        $this->get(route('tools.uuid.index'))->assertRedirect(route('login'));
    }

    public function test_uuid_rejects_unknown_version(): void
    {
        $this->loginAdmin();

        $response = $this->from(route('tools.uuid.index'))
            ->get(route('tools.uuid.index', ['version' => 'unknown']));

        $response->assertRedirect(route('tools.uuid.index'));
        $response->assertSessionHasErrors('version');
    }

    public function test_uuid_rejects_too_many(): void
    {
        $this->loginAdmin();

        $response = $this->from(route('tools.uuid.index'))
            ->get(route('tools.uuid.index', ['count' => 101]));

        $response->assertRedirect(route('tools.uuid.index'));
        $response->assertSessionHasErrors('count');
    }
}
