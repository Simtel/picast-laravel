<?php

declare(strict_types=1);

namespace App\Http\Controllers\Personal;

use App\Context\User\Domain\Model\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Personal\User\Update;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;

final class UsersController extends Controller
{
    /**
     * @param  User  $user
     *
     * @return Factory|View|Application
     */
    public function edit(User $user): Factory|View|Application
    {
        $roles = Role::all();
        return view('personal.user.edit', ['user' => $user, 'roles' => $roles]);
    }

    /**
     * @param  Update  $request
     * @param  User  $user
     *
     * @return RedirectResponse
     */
    public function update(User $user, Update $request): RedirectResponse
    {
        $user->update([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'birth_date' => $request->date('birth_date')?->format('Y-m-d'),
        ]);

        $user->syncRoles($request->all('roles'));

        $user->save();

        return redirect()->route('user.edit', [$user])->with('success', 'Пользователь успешно обновлен!');
    }
}
