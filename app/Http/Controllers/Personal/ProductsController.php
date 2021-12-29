<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductAddRequest;
use App\Models\MarketPlaces;
use App\Models\Products;
use App\Models\ProductsUrls;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 *
 */
class ProductsController extends Controller
{

    /**
     * @return Factory|View|Application
     */
    public function create(): Factory|View|Application
    {
        $market_places = MarketPlaces::all();
        return view('personal.prices.products.create', ['market_places' => $market_places]);
    }


    /**
     * @param  ProductAddRequest  $request
     *
     * @return RedirectResponse
     */
    public function store(ProductAddRequest $request): RedirectResponse
    {
        $product = Products::create(['name' => $request->get('name'), 'user_id' => Auth()->id()]);

        foreach ($request->get('urls') as $key => $url) {
            ProductsUrls::create(
                [
                    'product_id' => $product->id,
                    'marketplace_id' => $key,
                    'url' => $url['url']
                ]
            );
        }
        return redirect()->route('prices.index');
    }
}
