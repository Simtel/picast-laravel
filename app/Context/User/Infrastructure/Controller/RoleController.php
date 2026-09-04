<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Controller;

use App\Context\User\Application\Service\RoleService;
use App\Context\User\Infrastructure\Request\Personal\Role\Store;
use App\Context\User\Infrastructure\Request\Personal\Role\Update;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

final class RoleController extends Controller
{
    /**
     * RoleController constructor.
     */
    public function __construct(
        private readonly RoleService $roleService,
    ) {
        $this->middleware('can:edit user');
    }

    /**
     * Список ролей с поиском и сортировкой.
     *
     * @return Factory|View|Application
     */
    public function index(Request $request): Factory|View|Application
    {
        $search = $request->string('search')->toString();
        $sort = $request->string('sort', 'name')->toString();
        $direction = $request->string('direction', 'asc')->toString();

        $allowedSorts = ['name', 'users_count'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'name';
        }
        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'asc';
        }

        $query = Role::withCount('users');
        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        $roles = $query->orderBy($sort, $direction)->get();

        Log::debug('[RoleController.index] роли: search={search}, sort={sort}, direction={direction}, count={count}', [
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
            'count' => $roles->count(),
        ]);

        return view('personal.roles.index', [
            'roles' => $roles,
            'search' => $search,
            'currentSort' => $sort,
            'currentDirection' => $direction,
        ]);
    }

    /**
     * Форма создания роли.
     *
     * @return Factory|View|Application
     */
    public function create(): Factory|View|Application
    {
        return view('personal.roles.create', ['sections' => sections_list()]);
    }

    /**
     * Создание роли.
     *
     * @param  Store  $request
     * @return RedirectResponse
     */
    public function store(Store $request): RedirectResponse
    {
        $role = $this->roleService->create(
            $request->string('name')->toString(),
            $request->validated('sections', [])
        );

        return redirect()->route('roles.index')->with('success', 'Роль «' . $role->name . '» успешно создана!');
    }

    /**
     * Форма редактирования роли.
     *
     * @param  Role  $role
     * @return Factory|View|Application
     */
    public function edit(Role $role): Factory|View|Application
    {
        Log::debug('[RoleController.edit] редактирование роли {name}', ['name' => $role->name]);

        return view('personal.roles.edit', [
            'role' => $role,
            'sections' => sections_list(),
        ]);
    }

    /**
     * Обновление роли и её разделов.
     *
     * @param  Update  $request
     * @param  Role  $role
     * @return RedirectResponse
     */
    public function update(Role $role, Update $request): RedirectResponse
    {
        $this->roleService->update(
            $role,
            $request->string('name')->toString(),
            $request->validated('sections', [])
        );

        return redirect()->route('roles.edit', [$role])->with('success', 'Роль успешно обновлена!');
    }

    /**
     * Удаление роли.
     *
     * @param  Role  $role
     * @return RedirectResponse
     */
    public function destroy(Role $role): RedirectResponse
    {
        $result = $this->roleService->delete($role);

        if ($result !== true) {
            return redirect()->route('roles.index')->with('error', $result);
        }

        return redirect()->route('roles.index')->with('success', 'Роль успешно удалена!');
    }
}
