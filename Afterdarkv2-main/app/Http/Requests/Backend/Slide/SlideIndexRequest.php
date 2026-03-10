<?php

namespace App\Http\Requests\Backend\Slide;

use App\Constants\SongFormatConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SlideIndexRequest extends FormRequest
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
            'artistIds' => [
                'sometimes',
                'required',
                'array',
            ],
            'artistIds.*' => [
                'exists:artists,id',
            ],
            'albumIds' => [
                'sometimes',
                'required',
                'array',
            ],
            'albumIds.*' => [
                'exists:albums,id',
            ],
            'userIds' => [
                'sometimes',
                'required',
                'array',
            ],
            'userIds.*' => [
                'exists:users,id',
            ],
            'genre' => [
                'sometimes',
                'required',
                'array',
            ],
            'genre.*' => [
                'exists:genres,id',
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
            'duration_from' => [
                'sometimes',
                'required',
                'integer',
            ],
            'duration_until' => [
                'sometimes',
                'required',
                'integer',
            ],
            'format' => [
                'sometimes',
                'required',
                Rule::in(SongFormatConstants::getList()),
            ],
            'results_per_page' => [
                'sometimes',
                'required',
                'integer',
            ],
            'comment_disabled' => [
                'sometimes',
                'required',
                'accepted',
            ],
            'not_approved' => [
                'sometimes',
                'required',
                'accepted',
            ],
            'hidden' => [
                'sometimes',
                'required',
                'accepted',
            ],
        ];
    }
}
