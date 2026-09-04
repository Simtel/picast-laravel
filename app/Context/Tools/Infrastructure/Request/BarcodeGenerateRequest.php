<?php

declare(strict_types=1);

namespace App\Context\Tools\Infrastructure\Request;

use App\Context\Tools\Application\Service\BarcodeService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class BarcodeGenerateRequest extends FormRequest
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
            'type' => ['nullable', Rule::in(array_keys(app(BarcodeService::class)->getTypes()))],
            'text' => ['required_with:type', 'string', 'max:255'],
            'scale' => ['nullable', 'integer', 'between:1,5'],
            'height' => ['nullable', 'integer', 'between:20,200'],
        ];
    }
}
