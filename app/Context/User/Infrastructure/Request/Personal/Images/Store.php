<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Request\Personal\Images;

use Illuminate\Foundation\Http\FormRequest;

final class Store extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
