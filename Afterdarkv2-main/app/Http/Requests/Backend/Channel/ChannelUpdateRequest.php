<?php

namespace App\Http\Requests\Backend\Channel;

use App\Constants\TypeConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChannelUpdateRequest extends FormRequest
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
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'object_type' => [
                'required',
                Rule::in(TypeConstants::getChannelsList(false)),
            ],
            'object_ids' => [
                'nullable',
                'array',
            ],
            'object_ids.*' => [
                'nullable',
                'integer',
            ],
            'meta_title' => [
                'nullable',
                'string',
                'max:255',
            ],
            'meta_description' => [
                'nullable',
                'string',
            ],
            'is_visible' => [
                'required',
                'boolean',
            ],
            'allow_home' => [
                'required',
                'boolean',
            ],
            'allow_discover' => [
                'required',
                'boolean',
            ],
            'allow_radio' => [
                'required',
                'boolean',
            ],
            'allow_community' => [
                'required',
                'boolean',
            ],
            'allow_podcasts' => [
                'required',
                'boolean',
            ],
            'allow_trending' => [
                'required',
                'boolean',
            ],
            'genre' => [
                'sometimes',
                'required',
                'array',
            ],
            'genre.*' => [
                'sometimes',
                'required',
                'exists:genres,id',
            ],
            'radio_categories' => [
                'sometimes',
                'required',
                'array',
            ],
            'radio_categories.*' => [
                'sometimes',
                'required',
                'exists:radio_categories,id',
            ],
            'podcast_categories' => [
                'sometimes',
                'required',
                'array',
            ],
            'podcast_categories.*' => [
                'sometimes',
                'required',
                'exists:podcast_categories,id',
            ],
        ];
    }

    protected function passedValidation()
    {
        $this->merge([
            'alt_name' => str_slug($this->input('title')),
        ]);
    }
}
