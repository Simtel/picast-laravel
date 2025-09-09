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
            return view('personal.index', ['users' => User::all()]);
        }

        return redirect()->route('domains.index');
    }
}
