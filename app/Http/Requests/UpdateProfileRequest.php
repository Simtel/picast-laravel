<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int,string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->user()->id],
            'birth_date' => ['nullable', 'date', 'before:today'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Поле имя обязательно для заполнения.',
            'name.string' => 'Имя должно быть строкой.',
            'name.max' => 'Имя не должно превышать 255 символов.',
            'email.required' => 'Поле email обязательно для заполнения.',
            'email.email' => 'Email должен быть действительным адресом электронной почты.',
            'email.unique' => 'Такой email уже используется.',
            'birth_date.date' => 'Дата рождения должна быть действительной датой.',
            'birth_date.before' => 'Дата рождения должна быть в прошлом.',
        ];
    }
}
