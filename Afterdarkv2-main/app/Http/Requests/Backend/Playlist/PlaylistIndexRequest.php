<?php

namespace App\Http\Requests\Backend\Playlist;

use Illuminate\Foundation\Http\FormRequest;

class PlaylistIndexRequest extends FormRequest
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
            'hidden' => [
                'sometimes',
                'required',
                'accepted',
            ],
        ];
    }
}
