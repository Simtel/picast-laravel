<?php

namespace App\Http\Controllers\Personal;


use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;


class IndexController extends Controller
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
