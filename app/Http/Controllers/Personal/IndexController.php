<?php

namespace App\Http\Controllers\Personal;


use App\Contracts\InviteUserService;
use App\Contracts\SomeServiceContract;
use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\User;
use App\Models\Whois;
use App\Services\SomeService;
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
