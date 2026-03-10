<?php

namespace App\Http\Requests\Frontend\Episode;

use Illuminate\Foundation\Http\FormRequest;

class EpisodeEditRequest extends FormRequest
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
            'id' => [
                'required',
                'numeric',
                'exists:episodes,id',
            ],
            'title' => [
                'required',
                'string',
                'max:100',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'season' => [
                'nullable',
                'numeric',
            ],
            'number' => [
                'nullable',
                'numeric',
            ],
            'type' => [
                'nullable',
                'numeric',
                'in:1,2,3',
            ],
            'is_visible' => [
                'sometimes',
                'required',
                'boolean',
            ],
            'downloadable' => [
                'sometimes',
                'required',
                'boolean',
            ],
            'notification' => [
                'sometimes',
                'required',
                'boolean',
            ],
            'created_at' => [
                'nullable',
                'date_format:m/d/Y',
                'after:now',
            ],
        ];
    }
}
