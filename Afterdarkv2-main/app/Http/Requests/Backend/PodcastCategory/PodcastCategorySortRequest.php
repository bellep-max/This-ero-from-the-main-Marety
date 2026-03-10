<?php

namespace App\Http\Requests\Backend\PodcastCategory;

use Illuminate\Foundation\Http\FormRequest;

class PodcastCategorySortRequest extends FormRequest
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
            'categoryIds' => [
                'required',
                'array',
            ],
            'categoryIds.*' => [
                'required',
                'exists:podcast_categories,id',
            ],
        ];
    }
}
