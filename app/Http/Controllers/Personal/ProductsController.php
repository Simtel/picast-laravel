<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductAddRequest;
use App\Http\Requests\ProductUpdateRequest;
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

    /**
     * @param  Products  $product
     *
     * @return Factory|View|Application
     */
    public function edit(Products $product): Factory|View|Application
    {
        $market_places = MarketPlaces::all();

        return view('personal.prices.products.edit', ['product' => $product, 'market_places' => $market_places]);
    }

    /**
     * @param  ProductUpdateRequest  $request
     * @param  Products  $product
     *
     * @return RedirectResponse
     */
    public function update(ProductUpdateRequest $request, Products $product): RedirectResponse
    {
        $product->name = $request->get('name');
        foreach ($product->urls as $url) {
            if ($request->get('urls')) {
                $url->url = $request->get('urls')[$url->marketplace_id]['url'];
            }
        }
        $product->push();
        return redirect()->route('prices.index');
    }
}
