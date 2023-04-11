<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class IsCurrentPassword implements Rule
{
    /**
     * Create a new rule instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value): bool
    {
        $user = Auth::user();
        if ($user === null) {
            throw new BadRequestException('Not found user.');
        }
        if (!is_string($value)) {
            return false;
        }
        $current_password = $user->password;
        return Hash::check($value, $current_password);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'Введите правильный старый пароль.';
    }
}
