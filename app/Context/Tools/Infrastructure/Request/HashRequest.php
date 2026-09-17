<?php

declare(strict_types=1);

namespace App\Context\Tools\Infrastructure\Request;

use App\Context\Tools\Application\Service\HashService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class HashRequest extends FormRequest
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
            'algorithm' => ['nullable', Rule::in(array_keys(app(HashService::class)->getAlgorithms()))],
            'text' => ['required_with:algorithm', 'string', 'max:255'],
        ];
    }
}
