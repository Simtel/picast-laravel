<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Controller;

use App\Context\User\Domain\Model\User;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class IndexController extends Controller
{
    private const int PER_PAGE = 15;

    /**
     * Главная страница личного кабинета
     * @return Application|Factory|View|RedirectResponse
     */
    public function index(Request $request): View|Factory|RedirectResponse|Application
    {
        if ($request->user() !== null && $request->user()->hasRole('admin')) {
            $users = $this->getUsersWithSearchAndSort($request);
            return view('personal.index', ['users' => $users]);
        }

        return redirect()->route('domains.index');
    }

    /**
     * Получение пользователей с поиском и сортировкой
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \App\Context\User\Domain\Model\User>
     */
    private function getUsersWithSearchAndSort(Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $search = strval($request->query('search', ''));
        $sortColumn = strval($request->query('sort', 'created_at'));
        $sortDirection = strval($request->query('direction', 'desc'));

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

        return $query->paginate(self::PER_PAGE);
    }
}
