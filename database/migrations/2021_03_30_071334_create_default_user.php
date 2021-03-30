<?php

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDefaultUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $user = User::firstOrNew(
            [
                'email' => 'simtel@mail.ru'
            ]
        );

        $user->name = 'Simtel';
        $user->password = Hash::make('123456');
        $user->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {
            $user = User::findOrFail(['email' => 'simtel@mail.ru']);
            $user->delete();
        } catch (ModelNotFoundException | Exception $e) {

        }

    }
}
