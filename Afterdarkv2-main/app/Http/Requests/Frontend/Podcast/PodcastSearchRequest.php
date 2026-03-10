<?php

namespace App\Http\Requests\Frontend\Podcast;

use Illuminate\Foundation\Http\FormRequest;

class PodcastSearchRequest extends FormRequest
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
            'categories' => [
                'sometimes',
                'required',
                'array',
            ],
            'categories.*' => [
                'sometimes',
                'required',
                'string',
                'exists:categories,id',
            ],
            'languages' => [
                'sometimes',
                'required',
                'array',
            ],
            'languages.*' => [
                'sometimes',
                'required',
                'string',
                'exists:languages,id',
            ],
            'countries' => [
                'sometimes',
                'required',
                'array',
            ],
            'countries.*' => [
                'sometimes',
                'required',
                'string',
                'exists:countries,id',
            ],
            'regions' => [
                'sometimes',
                'required',
                'array',
            ],
            'regions.*' => [
                'sometimes',
                'required',
                'string',
                'exists:regions,id',
            ],
            'tags' => [
                'sometimes',
                'required',
                'array',
            ],
            'tags.*' => [
                'sometimes',
                'required',
                'string',
                'exists:post_tags,tag',
            ],
        ];
    }
}
