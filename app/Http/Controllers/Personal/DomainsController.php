<?php

namespace App\Http\Controllers\Personal;


use App\Http\Controllers\Controller;
use App\Models\Domain;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;



class DomainsController extends Controller
{
    /**
     * Главная страница личного кабинета
     * @return Factory|View
     */
    public function index()
    {
        $domains = Domain::whereUserId(Auth()->id())->get();
        return view('personal.domains',['domains' => $domains]);
    }
}
