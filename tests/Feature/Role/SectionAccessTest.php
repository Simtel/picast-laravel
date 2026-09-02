<?php

declare(strict_types=1);

namespace Tests\Feature\Role;

use App\Context\ChadGPT\Application\Service\ChadGptRequestService;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class SectionAccessTest extends TestCase
{
    /**
     * Раздел недоступен пользователю без пермишена.
     *
     * @param array{route: string, params?: array<string, int|string>} $section
     */
    #[DataProvider('sectionProvider')]
    public function test_section_requires_permission(array $section): void
    {
        $this->authUserWithPermissions([], []);

        $response = $this->get(route($section['route'], $section['params'] ?? []));

        $response->assertStatus(403);
    }

    /**
     * Раздел доступен пользователю с пермишеном.
     *
     * @param array{route: string, params?: array<string, int|string>, permission: string} $section
     */
    #[DataProvider('sectionProvider')]
    public function test_section_available_with_permission(array $section): void
    {
        if ($section['permission'] === 'view chadgpt') {
            $service = Mockery::mock(ChadGptRequestService::class);
            $service->shouldReceive('getModels')->once()->andReturn([]);
            app()->instance(ChadGptRequestService::class, $service);
        }

        $this->authUserWithPermissions([], [$section['permission']]);

        $response = $this->get(route($section['route'], $section['params'] ?? []));

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $this->get(route('settings'))->assertRedirect(route('login'));
    }

    public function test_settings_section_not_available_to_role_without_permission(): void
    {
        $this->authUserWithPermissions([], ['view chadgpt']);

        $this->get(route('settings'))->assertStatus(403);
    }

    public function test_member_like_role_without_dashboard_redirects_to_domains(): void
    {
        $this->authUserWithPermissions([], ['domains', 'view tournaments']);

        $this->get('/personal')->assertRedirect(route('domains.index'));
    }

    /**
     * @return array<string, array{array{route: string, params?: array<string, int|string>, permission: string}}>
     */
    public static function sectionProvider(): array
    {
        return [
            'chadgpt' => [['route' => 'chadgpt.index', 'permission' => 'view chadgpt']],
            'tournaments' => [['route' => 'tournaments.index', 'permission' => 'view tournaments']],
            'tools' => [['route' => 'tools.index', 'permission' => 'view tools']],
            'settings' => [['route' => 'settings', 'permission' => 'view settings']],
        ];
    }
}
