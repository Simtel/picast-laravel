<?php

namespace App\Http\Requests;

use App\Rules\UrlMartketPlace;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string|array<int, string|UrlMartketPlace>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:225'],
            'urls' => ['array', 'required', new UrlMartketPlace()]
        ];
    }
}
