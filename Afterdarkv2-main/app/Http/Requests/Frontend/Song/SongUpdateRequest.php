<?php

namespace App\Http\Requests\Frontend\Song;

use Illuminate\Foundation\Http\FormRequest;

class SongUpdateRequest extends FormRequest
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
            'vocal_id' => [
                'sometimes',
                'required',
                'exists:vocals,id',
            ],
            'genres' => [
                'sometimes',
                'required',
                'array',
            ],
            'genres.*' => [
                'sometimes',
                'required',
                'exists:genres,id',
            ],
            'is_visible' => [
                'required',
                'boolean',
            ],
            'is_explicit' => [
                'required',
                'boolean',
            ],
            'allow_comments' => [
                'required',
                'boolean',
            ],
            'allow_download' => [
                'required',
                'boolean',
            ],
            'is_patron' => [
                'required',
                'boolean',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'script' => [
                'nullable',
                'string',
                'max:500',
            ],
            'tags' => [
                'sometimes',
                'required',
                'array',
            ],
            'tags.*' => [
                'sometimes',
                'required',
            ],
            'artwork' => [
                'sometimes',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:' . config('settings.image_max_file_size'),
            ],
        ];
    }
}
