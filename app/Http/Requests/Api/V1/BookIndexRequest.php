<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BookIndexRequest extends FormRequest
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
        return [
            'q' => ['sometimes', 'string', 'max:255'],
            'author' => ['sometimes', 'string', 'max:255'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'language_id' => ['sometimes', 'integer', 'exists:languages,id'],
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'rating' => ['sometimes', 'integer', 'min:0', 'max:5'],
            'min_rating' => ['sometimes', 'integer', 'min:0', 'max:5'],
            'min_duration' => ['sometimes', 'integer', 'min:0'],
            'max_duration' => ['sometimes', 'integer', 'min:0', 'gte:min_duration'],
            'sort' => ['sometimes', 'string'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
