<?php

declare(strict_types=1);

namespace App\Context\Domains\Infrastructure\Request;

use App\Context\Domains\Infrastructure\Rule\DomainName;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class DomainRequest extends FormRequest
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
     * @return array<string, array<int, string|DomainName|Unique>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:225',
                new DomainName(),
                Rule::unique('domains')->where(function ($query) {
                    return $query->where('name', $this->get('name'))
                        ->where('user_id', Auth::id());
                })
            ]
        ];
    }
}
