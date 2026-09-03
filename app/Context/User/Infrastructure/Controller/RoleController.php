<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Controller;

use App\Context\User\Infrastructure\Request\Personal\Role\Store;
use App\Context\User\Infrastructure\Request\Personal\Role\Update;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class RoleController extends Controller
{
    /**
     * Роли, которые нельзя удалять.
     *
     * @var string[]
     */
    private const array PROTECTED_ROLES = ['admin'];

    /**
     * RoleController constructor.
     */
    public function __construct()
    {
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
        $role = Role::create(['name' => $request->string('name')->toString(), 'guard_name' => 'web']);

        $sections = $request->validated('sections', []);
        $permissions = $this->permissionsForSections($sections);
        if ($permissions !== []) {
            $role->givePermissionTo($permissions);
        }
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        Log::info('[RoleController.store] роль создана: {name}, разделов: {sections}', [
            'name' => $role->name,
            'sections' => $sections,
        ]);

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
        $role->name = $request->string('name')->toString();
        $role->save();

        $permissions = $this->permissionsForSections($request->validated('sections', []));
        $role->syncPermissions($permissions);
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        Log::info('[RoleController.update] роль обновлена: {name}, разделов: {count}', [
            'name' => $role->name,
            'count' => count($permissions),
        ]);

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
        if (in_array($role->name, self::PROTECTED_ROLES, true)) {
            Log::warning('[RoleController.destroy] попытка удалить защищённую роль {name}', ['name' => $role->name]);

            return redirect()->route('roles.index')->with('error', 'Роль «' . $role->name . '» нельзя удалить.');
        }

        if ($role->users()->exists()) {
            Log::warning('[RoleController.destroy] роль {name} назначена пользователям, удаление отклонено', [
                'name' => $role->name,
            ]);

            return redirect()->route('roles.index')->with('error', 'Роль назначена пользователям и не может быть удалена.');
        }

        $role->delete();
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        Log::info('[RoleController.destroy] роль удалена: {name}', ['name' => $role->name]);

        return redirect()->route('roles.index')->with('success', 'Роль успешно удалена!');
    }

    /**
     * Маппинг ключей разделов из каталога на их пермишены.
     *
     * @param  array<int, string>  $sectionKeys
     * @return array<int, string>
     */
    private function permissionsForSections(array $sectionKeys): array
    {
        $permissions = [];
        foreach ($sectionKeys as $key) {
            $permission = section_permission($key);
            if ($permission !== null) {
                $permissions[] = $permission;
            }
        }

        // Гарантируем существование пермишенов (могут быть новые, созданные не миграцией).
        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName);
        }

        return array_values(array_unique($permissions));
    }
}
