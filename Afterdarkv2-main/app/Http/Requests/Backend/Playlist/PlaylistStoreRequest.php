<?php

namespace App\Http\Requests\Backend\Playlist;

use Illuminate\Foundation\Http\FormRequest;

class PlaylistStoreRequest extends FormRequest
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
                'max:100',
            ],
            'user_id' => [
                'required',
                'exists:users,id',
            ],
            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],
            'genre' => [
                'sometimes',
                'required',
                'array',
            ],
            'genre.*' => [
                'exists:genres,id',
            ],
            'artwork' => [
                'sometimes',
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:' . config('settings.image_max_file_size'),
            ],
        ];
    }
}
