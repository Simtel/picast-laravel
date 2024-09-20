<?php

declare(strict_types=1);

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Personal\User\Update;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
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
        $user->name = $request->string('name')->toString();
        $user->email = $request->string('email')->toString();
        $user->syncRoles($request->all('roles'));
        $user->save();
        return redirect()->route('user.edit', [$user]);
    }
}
