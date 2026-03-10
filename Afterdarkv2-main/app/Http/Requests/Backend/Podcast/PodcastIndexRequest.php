<?php

namespace App\Http\Requests\Backend\Podcast;

use App\Constants\SearchConstants;
use App\Constants\TypeConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PodcastIndexRequest extends FormRequest
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
            'term' => [
                'sometimes',
                'required_with:location',
                'string',
            ],
            'location' => [
                'sometimes',
                'required_with:term',
                Rule::in(SearchConstants::getCodesList(TypeConstants::STATION)),
            ],
            'userIds' => [
                'sometimes',
                'required',
                'array',
            ],
            'userIds.*' => [
                'sometimes',
                'required',
                'exists:users,id',
            ],
            'created_from' => [
                'sometimes',
                'required',
                'date',
            ],
            'created_until' => [
                'sometimes',
                'required',
                'date',
            ],
            'comment_count_from' => [
                'sometimes',
                'required',
                'integer',
            ],
            'comment_count_until' => [
                'sometimes',
                'required',
                'integer',
            ],
            'country' => [
                'sometimes',
                'required',
                'exists:country,code',
            ],
            'city_id' => [
                'sometimes',
                'required_with:country',
                'exists:city,id',
            ],
            'comment_disabled' => [
                'sometimes',
                'required',
                'boolean',
            ],
            'hidden' => [
                'sometimes',
                'required',
                'boolean',
            ],
        ];
    }
}
