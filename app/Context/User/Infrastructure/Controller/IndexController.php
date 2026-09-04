<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Controller;

use App\Context\User\Application\Query\UserListingQuery;
use App\Context\User\Domain\Model\User;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class IndexController extends Controller
{
    public function __construct(
        private readonly UserListingQuery $userListingQuery,
    ) {
    }

    /**
     * Главная страница личного кабинета
     * @return Application|Factory|View|RedirectResponse
     */
    public function index(Request $request): View|Factory|RedirectResponse|Application
    {
        if ($request->user() !== null && $request->user()->hasPermissionTo('view dashboard')) {
            $users = $this->userListingQuery->handle($request->query());
            return view('personal.index', ['users' => $users]);
        }

        return redirect()->route('domains.index');
    }
}
