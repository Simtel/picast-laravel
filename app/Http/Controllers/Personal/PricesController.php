<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\MarketPlaces;
use App\Models\Products;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class PricesController extends Controller
{

    /**
     * @return Factory|View|Application
     */
    public function index(): Factory|View|Application
    {
        $products = Products::whereUserId(Auth()->id())->with('urls')->get();
        $marketplaces = MarketPlaces::all();
        return view('personal.prices.index', ['products' => $products, 'marketplaces' => $marketplaces]);
    }

    /**
     * @param  Products  $product
     *
     * @return void
     */
    public function show(Products $product): void
    {
        response('', 404);
    }
}
