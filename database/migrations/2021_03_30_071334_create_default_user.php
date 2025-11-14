<?php

declare(strict_types=1);

use App\Context\User\Domain\Model\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $user = User::firstOrNew(
            [
                'email' => getenv('DEFAULT_USER_EMAIL')
            ]
        );

        $user->name = (string)getenv('DEFAULT_USER_NAME');
        $user->password = Hash::make(getenv('DEFAULT_USER_PASSWORD'));
        $user->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        try {
            $user = User::findOrFail(['email' => getenv('DEFAULT_USER_EMAIL')]);
            $user = current($user);
            if ($user instanceof User) {
                $user->delete();
            }
        } catch (ModelNotFoundException|Exception $e) {
        }
    }
}
