<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
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
     * @param  ProductRequest  $request
     *
     * @return RedirectResponse
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        $product = Products::create(['name' => $request->get('name'), 'user_id' => Auth()->id()]);

        foreach ($request->get('urls') as $key => $url) {
            ProductsUrls::create(
                [
                    'product_id' => $product->id,
                    'marketplace_id' => $key,
                    'url' => $url
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
     * @param  ProductRequest  $request
     * @param  Products  $product
     *
     * @return RedirectResponse
     */
    public function update(ProductRequest $request, Products $product): RedirectResponse
    {
        $product->name = $request->get('name');
        $urls = [];
        foreach ($request->get('urls') as $marketplace_id => $value) {
            $productUrl = $product->urls->firstWhere('marketplace_id', $marketplace_id);
            if ($productUrl === null) {
                $productUrl = new ProductsUrls();
            }
            $productUrl->url = $value;
            $urls[] = $productUrl;
        }
        $product->urls()->saveMany($urls);
        $product->save();
        return redirect()->route('prices.index');
    }
}