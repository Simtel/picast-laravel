<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Infrastructure\Request;

use Illuminate\Foundation\Http\FormRequest;

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
            'model' => 'nullable|string|in:gpt-5,gpt-5-mini,gpt-5-nano,gpt-4o-mini,gpt-4o,claude-3-haiku,claude-3-opus,claude-4.5-sonnet,claude-3.7-sonnet-thinking,claude-4.1-opus,gemini-2.0-flash,gemini-2.5-pro,deepseek-v3.1'
        ];
    }
}
