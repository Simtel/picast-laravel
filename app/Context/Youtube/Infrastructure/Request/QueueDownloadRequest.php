<?php

declare(strict_types=1);

namespace App\Context\Youtube\Infrastructure\Request;

use Illuminate\Foundation\Http\FormRequest;

final class QueueDownloadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'format_id' => ['required', 'integer', 'exists:video_formats,id'],
        ];
    }
}
