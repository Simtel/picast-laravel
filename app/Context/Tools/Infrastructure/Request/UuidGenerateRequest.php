<?php

declare(strict_types=1);

namespace App\Context\Tools\Infrastructure\Request;

use App\Context\Tools\Application\Service\UuidService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UuidGenerateRequest extends FormRequest
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
            'version' => ['nullable', Rule::in(array_keys(app(UuidService::class)->getVersions()))],
            'count' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
