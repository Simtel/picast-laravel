<?php

declare(strict_types=1);

namespace Tests\Feature\Role;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

final class RoleManagementTest extends TestCase
{
    public function test_roles_page_accessible_for_admin(): void
    {
        $this->loginAdmin();

        $response = $this->get(route('roles.index'));

        $response->assertStatus(200);
        $response->assertViewIs('personal.roles.index');
        $response->assertSee('Роли и доступ');
        $response->assertSee('admin');
    }

    public function test_roles_page_denied_for_user_without_edit_user(): void
    {
        $this->authUserWithPermissions([], ['view settings']);

        $this->get(route('roles.index'))->assertStatus(403);
    }

    public function test_create_role_with_sections(): void
    {
        $this->loginAdmin();

        $response = $this->post(route('roles.store'), [
            'name' => 'manager',
            'sections' => ['domains', 'tools'],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $role = Role::findByName('manager');
        self::assertTrue($role->hasPermissionTo('domains'));
        self::assertTrue($role->hasPermissionTo('view tools'));
        self::assertFalse($role->hasPermissionTo('view settings'));
    }

    public function test_create_role_validates_unique_name(): void
    {
        $this->loginAdmin();

        $this->post(route('roles.store'), ['name' => 'admin'])->assertSessionHasErrors(['name']);
    }

    public function test_update_role_sections(): void
    {
        $this->loginAdmin();

        $role = Role::create(['name' => 'manager', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::findOrCreate('domains'));
        $role->givePermissionTo(Permission::findOrCreate('view tools'));

        $response = $this->put(route('roles.update', $role), [
            'name' => 'manager',
            'sections' => ['settings', 'images'],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $role->refresh();
        self::assertFalse($role->hasPermissionTo('domains'));
        self::assertFalse($role->hasPermissionTo('view tools'));
        self::assertTrue($role->hasPermissionTo('view settings'));
        self::assertTrue($role->hasPermissionTo('edit images'));
    }

    public function test_destroy_protected_admin_role_is_rejected(): void
    {
        $this->loginAdmin();

        $admin = Role::findByName('admin');

        $response = $this->delete(route('roles.destroy', $admin));

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('roles', ['id' => $admin->id]);
    }

    public function test_destroy_role_assigned_to_users_is_rejected(): void
    {
        $this->loginAdmin();

        $user = $this->createUserWithPermissions([], ['domains']);
        $role = $user->roles()->first();

        $response = $this->delete(route('roles.destroy', $role));

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    public function test_destroy_unused_role(): void
    {
        $this->loginAdmin();

        $role = Role::create(['name' => 'temp-role', 'guard_name' => 'web']);

        $response = $this->delete(route('roles.destroy', $role));

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        self::assertNull(Role::where('name', 'temp-role')->first());
    }

    public function test_create_role_denied_for_non_admin(): void
    {
        $this->authUserWithPermissions([], []);

        $this->post(route('roles.store'), ['name' => 'x'])->assertStatus(403);
    }
}
