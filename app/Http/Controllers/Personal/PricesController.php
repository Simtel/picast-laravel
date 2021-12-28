<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class PricesController extends Controller
{

    public function index(): Factory|View|Application
    {
        return view('personal.prices.index');
    }

}
