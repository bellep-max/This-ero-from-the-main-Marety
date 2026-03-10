<?php

namespace App\Http\Requests\Backend\Artist;

use Illuminate\Foundation\Http\FormRequest;

class ArtistStoreRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'unique:artists,name',
            ],
            'artwork' => [
                'sometimes',
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:' . config('settings.image_max_file_size'),
            ],
            'bio' => [
                'nullable',
                'string',
            ],
            'genre' => [
                'sometimes',
                'array',
            ],
            'genre.*' => [
                'exists:genres,id',
            ],
        ];
    }
}
