<?php

namespace App\Http\Requests;

use App\Facades\Domains\WhoisService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * @return array
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
