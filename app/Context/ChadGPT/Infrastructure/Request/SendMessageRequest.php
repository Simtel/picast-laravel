<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Infrastructure\Request;

use App\Context\ChadGPT\Domain\ChatModels;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class SendMessageRequest extends FormRequest
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
