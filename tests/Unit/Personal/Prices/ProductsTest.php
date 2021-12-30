<?php

namespace Tests\Unit\Personal\Prices;


use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\TestCase;


class ProductsTest extends TestCase
{


    public function test_validate_product_request()
    {
        $attributes = [
            'name' => 'Детское молочко Nutricia Малютка 4, 600 г',
            'urls' => [
                1
            ]
        ];
        $request = new ProductRequest();
        $rules = $request->rules();
        $validator = Validator::make($attributes, $rules);
        $fails = $validator->fails();
        $this->assertEquals(false, $fails);
    }
}