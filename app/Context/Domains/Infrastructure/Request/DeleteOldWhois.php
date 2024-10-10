<?php

declare(strict_types=1);

namespace App\Context\Domains\Infrastructure\Request;

use App\Context\Domains\Infrastructure\Facades\WhoisService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

class DeleteOldWhois extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string|In>>
     */
    public function rules(): array
    {
        return [
            'delete_old_whois' => [
                'required',
                Rule::in(array_keys(WhoisService::getTimeFrameOptions()))
            ]
        ];
    }
}
