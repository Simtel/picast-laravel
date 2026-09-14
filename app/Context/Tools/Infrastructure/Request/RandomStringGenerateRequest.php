<?php

declare(strict_types=1);

namespace App\Context\Tools\Infrastructure\Request;

use Illuminate\Foundation\Http\FormRequest;

final class RandomStringGenerateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'length' => ['nullable', 'integer', 'between:1,128'],
            'uppercase' => ['nullable', 'boolean'],
            'lowercase' => ['nullable', 'boolean'],
            'digits' => ['nullable', 'boolean'],
            'symbols' => ['nullable', 'boolean'],
        ];
    }
}
