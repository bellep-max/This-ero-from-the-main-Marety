<?php

namespace App\Http\Requests\Frontend\Playlist;

use Illuminate\Foundation\Http\FormRequest;

class PlaylistAddSongRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'song_uuid' => [
                'required',
                'uuid',
                'exists:songs,uuid',
            ],
        ];
    }
}
