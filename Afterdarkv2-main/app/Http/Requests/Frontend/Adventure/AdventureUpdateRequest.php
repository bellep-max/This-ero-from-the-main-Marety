<?php

namespace App\Http\Requests\Frontend\Adventure;

use Illuminate\Foundation\Http\FormRequest;

class AdventureUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($genres = $this->input('genres')) {
            $ids = array_column($genres, 'value');

            $this->merge([
                'genres' => $ids,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:60',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'is_visible' => [
                'required',
                'boolean',
            ],
            'allow_comments' => [
                'required',
                'boolean',
            ],
            'genres' => [
                'required',
                'array',
            ],
            'genres.*' => [
                'sometimes',
                'required',
                'exists:genres,id',
            ],
            'tags' => [
                'sometimes',
                'required',
                'array',
            ],
            'artwork' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:' . config('settings.image_max_file_size'),
            ],
        ];
    }
}
