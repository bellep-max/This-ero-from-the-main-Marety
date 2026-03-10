<?php

namespace App\Http\Requests\Frontend\Podcast;

use Illuminate\Foundation\Http\FormRequest;

class PodcastEditRequest extends FormRequest
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
                'max:50',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'category' => [
                'required',
                'array',
            ],
            'category.*' => [
                'required',
                'exists:podcast_categories,id',
            ],
            'language_id' => [
                'nullable',
                'exists:languages,id',
            ],
            'country_id' => [
                'nullable',
                'string',
                'max:3',
            ],
            'is_visible' => [
                'sometimes',
                'required',
                'boolean',
            ],
            'allow_comments' => [
                'sometimes',
                'required',
                'boolean',
            ],
            'allow_download' => [
                'sometimes',
                'required',
                'boolean',
            ],
            'explicit' => [
                'sometimes',
                'required',
                'boolean',
            ],
            'artwork' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:' . config('settings.image_max_file_size'),
            ],
            'released_at' => [
                'nullable',
                'date_format:m/d/Y',
                'before:now',
            ],
            'created_at' => [
                'nullable',
                'date_format:m/d/Y',
                'after:now',
            ],
        ];
    }
}
