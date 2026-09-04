<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Controller;

use App\Context\User\Domain\Model\User;
use App\Context\User\Application\Service\ProfileUpdateService;
use App\Context\User\Infrastructure\Request\Personal\User\Update;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;

final class UsersController extends Controller
{
    public function __construct(
        private readonly ProfileUpdateService $profileUpdateService,
    ) {
    }

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
        $this->profileUpdateService->update($user, $request->validated());

        $user->syncRoles($request->validated('roles', []));

        return redirect()->route('user.edit', [$user])->with('success', 'Пользователь успешно обновлен!');
    }
}
