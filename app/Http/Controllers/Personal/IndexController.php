<?php

namespace App\Http\Controllers\Personal;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;


class IndexController extends Controller
{
    /**
     * Главная страница личного кабинета
     * @return Factory|View
     */
    public function index()
    {
        return view('personal.index',['users' => User::all()]);
    }
}
