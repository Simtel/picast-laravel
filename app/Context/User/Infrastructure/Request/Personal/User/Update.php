<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Request\Personal\User;

use App\Context\User\Domain\Model\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

final class Update extends FormRequest
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
        $routeUser = $this->route('user');
        $ignoreId = $routeUser instanceof User ? $routeUser->getId() : $routeUser;

        return [
            'name' => [
                'required',
                'max:225',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($ignoreId),
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
