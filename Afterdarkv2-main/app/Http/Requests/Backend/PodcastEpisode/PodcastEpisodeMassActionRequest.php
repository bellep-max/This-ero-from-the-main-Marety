<?php

namespace App\Http\Requests\Backend\PodcastEpisode;

use Illuminate\Foundation\Http\FormRequest;

class PodcastEpisodeMassActionRequest extends FormRequest
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
            'ids' => [
                'required',
                'array',
            ],
            'ids.*' => [
                'required',
                'exists:posts,id',
            ],
            'action' => [
                'required',
                'string',
            ],
        ];
    }
}
