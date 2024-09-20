<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddPricePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => 'edit prices']);
        $roles = Role::all();
        foreach ($roles as $role) {
            $role->givePermissionTo('edit prices');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $roles = Role::all();
        foreach ($roles as $role) {
            $role->revokePermissionTo('edit prices');
        }
    }
}
