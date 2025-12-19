<?php

declare(strict_types=1);

namespace App\Http\Controllers\Personal;

use App\Context\User\Domain\Model\User;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class IndexController extends Controller
{
    /**
     * Главная страница личного кабинета
     * @return Application|Factory|View|RedirectResponse
     */
    public function index(): View|Factory|RedirectResponse|Application
    {
        if (Auth::user() !== null && Auth::user()->hasRole('admin')) {
            $users = $this->getUsersWithSearchAndSort();
            return view('personal.index', ['users' => $users]);
        }

        return redirect()->route('domains.index');
    }

    /**
     * Получение пользователей с поиском и сортировкой
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \App\Context\User\Domain\Model\User>
     */
    private function getUsersWithSearchAndSort(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $search = strval(request('search') ?? '');
        $sortColumn = strval(request('sort') ?? 'created_at');
        $sortDirection = strval(request('direction') ?? 'desc');

        $query = User::query();

        // Поиск по имени и email
        if (!empty($search)) {
            $query->where(static function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Сортировка
        $allowedColumns = ['name', 'email', 'created_at', 'birth_date'];
        if (in_array($sortColumn, $allowedColumns)) {
            $query->orderBy($sortColumn, $sortDirection);
        }

        return $query->paginate(15);
    }
}
