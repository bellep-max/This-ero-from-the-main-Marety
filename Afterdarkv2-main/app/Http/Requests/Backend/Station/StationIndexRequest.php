<?php

namespace App\Http\Requests\Backend\Station;

use App\Constants\SearchConstants;
use App\Constants\TypeConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StationIndexRequest extends FormRequest
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
                'required',
                'string',
            ],
            'location' => [
                'sometimes',
                'required',
                Rule::in(SearchConstants::getCodesList(TypeConstants::STATION)),
            ],
            'userIds' => [
                'sometimes',
                'required',
                'array',
            ],
            'userIds.*' => [
                'exists:users,id',
            ],
            'category' => [
                'sometimes',
                'required',
                'array',
            ],
            'category.*' => [
                'exists:categories,id',
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
                'exists:countries,code',
            ],
            'city' => [
                'sometimes',
                'required_with:country',
                'exists:city,id',
            ],
            'language' => [
                'sometimes',
                'required_with:country',
                'exists:languages,id',
            ],
            'results_per_page' => [
                'sometimes',
                'required',
                'integer',
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
