<?php

declare(strict_types=1);

namespace App\Context\User\Application\Service;

use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class RoleService
{
    /**
     * Роли, которые нельзя удалять.
     *
     * @var string[]
     */
    private const array PROTECTED_ROLES = ['admin'];

    /**
     * @param array<int, string> $sections
     */
    public function create(string $name, array $sections): Role
    {
        /** @var Role $role */
        $role = Role::create(['name' => $name, 'guard_name' => 'web']);

        $permissions = $this->permissionsForSections($sections);
        if ($permissions !== []) {
            $role->givePermissionTo($permissions);
        }
        $this->forgetCachedPermissions();

        Log::info('[RoleService.create] роль создана', ['name' => $name, 'sections' => $sections]);

        return $role;
    }

    /**
     * @param array<int, string> $sections
     */
    public function update(Role $role, string $name, array $sections): void
    {
        $role->name = $name;
        $role->save();

        $permissions = $this->permissionsForSections($sections);
        $role->syncPermissions($permissions);
        $this->forgetCachedPermissions();

        Log::info('[RoleService.update] роль обновлена', ['name' => $name, 'sections' => $sections]);
    }

    /**
     * @return bool|string true при удалении, иначе текст ошибки
     */
    public function delete(Role $role): bool|string
    {
        if (in_array($role->name, self::PROTECTED_ROLES, true)) {
            Log::warning('[RoleService.delete] удаление отклонено', [
                'reason' => 'protected',
                'role' => $role->name,
            ]);

            return 'Роль «' . $role->name . '» нельзя удалить.';
        }

        if ($role->users()->exists()) {
            Log::warning('[RoleService.delete] удаление отклонено', [
                'reason' => 'assigned',
                'role' => $role->name,
            ]);

            return 'Роль назначена пользователям и не может быть удалена.';
        }

        $role->delete();
        $this->forgetCachedPermissions();

        Log::info('[RoleService.delete] роль удалена', ['name' => $role->name]);

        return true;
    }

    private function forgetCachedPermissions(): void
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Маппинг ключей разделов из каталога на их пермишены.
     *
     * @param array<int, string> $sectionKeys
     * @return array<int, string>
     */
    private function permissionsForSections(array $sectionKeys): array
    {
        $permissions = [];
        foreach ($sectionKeys as $key) {
            $permission = section_permission($key);
            if ($permission !== null) {
                $permissions[] = $permission;
            }
        }

        // Гарантируем существование пермишенов (могут быть новые, созданные не миграцией).
        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName);
        }

        return array_values(array_unique($permissions));
    }
}
