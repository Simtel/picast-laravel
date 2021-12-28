<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\MarketPlaces;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductsController extends Controller
{

    public function create(): Factory|View|Application
    {
        $market_places = MarketPlaces::all();
        return view('personal.prices.products.create', ['market_places' => $market_places]);
    }


    public function store(Request $request): void
    {
    }
}
