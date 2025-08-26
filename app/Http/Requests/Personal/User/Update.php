<?php

declare(strict_types=1);

namespace App\Http\Requests\Personal\User;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

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
     * @return array<string, mixed>
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
            ],
            'birth_date' => [
                'nullable',
                'date',
                'before:today'
            ],
            'roles' => [
                'nullable',
                'array'
            ],
            'roles.*' => [
                'string',
                Rule::in(Role::pluck('name')->toArray())
            ]
        ];
    }
}
