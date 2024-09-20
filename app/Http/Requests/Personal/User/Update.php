<?php

declare(strict_types=1);

namespace App\Http\Requests\Personal\User;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        if ($this->user() !== null) {
            return $this->user()->hasRole('admin');
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:225',
            ],
            'email' => [
                'required',
                'email'
            ]
        ];
    }
}
