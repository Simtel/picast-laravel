<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class YouTubeUrlRequest extends FormRequest
{
    /**
     * Определите, авторизован ли пользователь создавать данный запрос.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     *  @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'url' => ['required', 'string', 'regex:/^(https?\:\/\/)?(www\.youtube\.com|youtu\.?be)\/.+$/']
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'url.required' => 'Поле url является обязательным.',
            'url.string'   => 'Поле url должно быть строкой.',
            'url.regex'    => 'Поле url должно содержать валидную ссылку на видео YouTube.',
        ];
    }

}
