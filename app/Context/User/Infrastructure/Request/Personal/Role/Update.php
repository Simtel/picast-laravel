<?php

declare(strict_types=1);

namespace App\Context\User\Infrastructure\Request\Personal\Role;

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
        /** @var Role $role */
        $role = $this->route('role');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role->getKey(), $role->getKeyName()),
            ],
            'sections' => [
                'nullable',
                'array',
            ],
            'sections.*' => [
                'string',
                Rule::in(array_keys(sections_list())),
            ],
        ];
    }
}
