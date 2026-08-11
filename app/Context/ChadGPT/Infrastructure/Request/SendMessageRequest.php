<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Infrastructure\Request;

use App\Context\ChadGPT\Application\Service\ChadGptRequestService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

final class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $modelIds = app(ChadGptRequestService::class)->getModelIds();

        $modelRule = ['nullable', 'string', 'max:100'];

        if ($modelIds !== []) {
            $modelRule[] = Rule::in($modelIds);
        }

        return [
            'message' => 'required|string|max:1000',
            'model' => $modelRule,
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
