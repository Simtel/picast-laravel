<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

class FixRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $member = Role::findByName('member');
        $member->givePermissionTo('domains');
        $admin = Role::findByName('admin');
        $admin->givePermissionTo('invite user');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
