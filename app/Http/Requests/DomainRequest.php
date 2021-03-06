<?php

namespace App\Http\Requests;

use App\Rules\FQDN;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:225',
                new FQDN(),
                Rule::unique('domains')->where(function ($query) {
                    return $query->where('name', $this->get('name'))
                        ->where('user_id', Auth::id());
                })
            ]
        ];
    }
}
