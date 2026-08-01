<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $allowedExtensions = array_merge(
            config('audio.allowed_document_mimes'),
            config('audio.allowed_audio_mimes'),
        );

        return [
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'file' => [
                'required',
                'file',
                'max:'.config('audio.upload_max_size_kb'),
                'mimes:'.implode(',', $allowedExtensions),
            ],
        ];
    }
}
