<?php

namespace App\Http\Requests;

use App\Rules\IsCurrentPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int,IsCurrentPassword|Password|string>>
     */
    public function rules(): array
    {
        return [
            'password' => ['required', Password::min(6), new IsCurrentPassword()],
            'new_password' => ['required', Password::min(8)]
        ];
    }
}
