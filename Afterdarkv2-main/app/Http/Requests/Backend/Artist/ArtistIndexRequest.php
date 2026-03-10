<?php

namespace App\Http\Requests\Backend\Artist;

use Illuminate\Foundation\Http\FormRequest;

class ArtistIndexRequest extends FormRequest
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
            'genre' => [
                'sometimes',
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
            'followers_from' => [
                'sometimes',
                'required',
                'integer',
            ],
            'followers_until' => [
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
                'boolean',
            ],
            'verified' => [
                'sometimes',
                'required',
                'boolean',
            ],
        ];
    }
}
