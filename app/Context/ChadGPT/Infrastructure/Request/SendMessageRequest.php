<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Infrastructure\Request;

use App\Context\ChadGPT\Domain\ChatModels;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

final class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'message' => 'required|string|max:1000',
            'model' => 'nullable|string|in:' . implode(',', ChatModels::values()),
            'temperature' => 'nullable|numeric|between:0,2',
            'max_tokens' => 'nullable|integer|min:1',
            'images' => 'nullable|array|max:5',
            'images.*' => 'nullable|string',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $errors = new ValidationException($validator)->errors();

        throw new HttpResponseException(
            response()->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
