<?php

namespace Tests\Unit\Personal\Prices;


use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\MarketPlaces;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\TestCase;


class ProductsTest extends TestCase
{

    /**
     * @return void
     */
    public function test_validate_product_request(): void
    {
        $marketplaces = MarketPlaces::all();
        $attributes = [
            'name' => 'Детское молочко Nutricia Малютка 4, 600 г',
        ];
        foreach ($marketplaces as $market) {
            $attributes['urls'][$market->id] = $market->url.'lint_to_product';
        }
        $request = new ProductRequest();
        $rules = $request->rules();
        $validator = Validator::make($attributes, $rules);
        $fails = $validator->fails();
        $this->assertEquals(false, $fails);
    }
}