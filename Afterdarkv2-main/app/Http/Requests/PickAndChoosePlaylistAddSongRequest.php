<?php

namespace App\Http\Requests;

class PickAndChoosePlaylistAddSongRequest extends PickAndChoosePlaylistBasedRequest
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
     *
     * @return array
     */
    public function rules()
    {
        return [
            'song_id' => ['required', 'exists:songs,id'],
            'playlist_id' => ['required', 'exists:pick_and_choose_playlists,id'],
        ];
    }
}
