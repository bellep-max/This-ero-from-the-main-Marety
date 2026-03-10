<?php

namespace App\Http\Requests;

use App\Models\PickAndChoosePlaylistSong;

class PickAndChoosePlaylistAddChildRequest extends PickAndChoosePlaylistBasedRequest
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
            'song_id' => ['required', 'exists:pick_and_choose_playlist_song,id'],
            'child_id' => ['required', 'exists:pick_and_choose_playlist_song,id'],
        ];
    }

    public function getSong()
    {
        return PickAndChoosePlaylistSong::find($this->song_id);
    }

    public function getChild()
    {
        return PickAndChoosePlaylistSong::find($this->child_id);
    }
}
