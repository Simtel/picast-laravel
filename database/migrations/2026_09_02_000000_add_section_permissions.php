<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $newPermissions = [
            'view dashboard',
            'view chadgpt',
            'view tournaments',
            'view tools',
            'view settings',
        ];

        foreach ($newPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        $roles = Role::whereIn('name', ['admin', 'member'])->get();
        foreach ($roles as $role) {
            $granted = match ($role->name) {
                'admin' => $newPermissions,
                'member' => ['view chadgpt', 'view tournaments', 'view tools', 'view settings'],
                default => [],
            };

            if ($granted !== []) {
                $role->givePermissionTo($granted);
            }
        }

        Log::info('[migrate] созданы пермишены разделов: {permissions}', [
            'permissions' => implode(', ', $newPermissions),
        ]);
        Log::info('[migrate] права разделов выданы ролям admin/member');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $sectionPermissions = [
            'view dashboard',
            'view chadgpt',
            'view tournaments',
            'view tools',
            'view settings',
        ];

        $roles = Role::whereIn('name', ['admin', 'member'])->get();
        foreach ($roles as $role) {
            $role->revokePermissionTo($sectionPermissions);
        }

        Permission::whereIn('name', $sectionPermissions)->delete();
    }
};
