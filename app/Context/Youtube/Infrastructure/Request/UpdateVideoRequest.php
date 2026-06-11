<?php

declare(strict_types=1);

namespace App\Context\Youtube\Infrastructure\Request;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string'],
            'description' => ['sometimes', 'string'],
        ];
    }
}
