<?php

namespace App\Http\Requests\Frontend\Playlist;

use Illuminate\Foundation\Http\FormRequest;

class PlaylistStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
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
                'max:100',
            ],
            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],
            'genres' => [
                'sometimes',
                'required',
                'array',
            ],
            'genres.*' => [
                'exists:genres,id',
            ],
            'artwork' => [
                'sometimes',
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:' . config('settings.image_max_file_size'),
            ],
            'is_visible' => [
                'required',
                'boolean',
            ],
            'allow_comments' => [
                'required',
                'boolean',
            ],
            'is_explicit' => [
                'sometimes',
                'required',
                'boolean',
            ],
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'title' => strip_tags($this->input('title')),
            'description' => strip_tags($this->input('description')),
        ]);
    }
}
