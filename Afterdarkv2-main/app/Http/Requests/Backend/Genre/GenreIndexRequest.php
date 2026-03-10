<?php

namespace App\Http\Requests\Backend\Genre;

use Illuminate\Foundation\Http\FormRequest;

class GenreIndexRequest extends FormRequest
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
        ];
    }
}
