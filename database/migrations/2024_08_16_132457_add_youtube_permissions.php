<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
        Permission::create(['name' => 'edit youtube']);
        $roles = Role::all();
        foreach ($roles as $role) {

            $role->givePermissionTo('edit youtube');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $roles = Role::all();
        foreach ($roles as $role) {
            $role->revokePermissionTo('edit youtube');
        }
    }
};
