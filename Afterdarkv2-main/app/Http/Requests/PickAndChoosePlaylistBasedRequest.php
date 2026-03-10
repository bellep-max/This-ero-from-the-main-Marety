<?php

namespace App\Http\Requests;

use App\Models\PickAndChoosePlaylist;
use App\Models\Song;
use Illuminate\Foundation\Http\FormRequest;

class PickAndChoosePlaylistBasedRequest extends FormRequest
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
            'playlist_id' => ['required', 'exists:pick_and_choose_playlists,id'],
            'song_id' => ['required', 'exists:pick_and_choose_playlist_song,id'],
        ];
    }

    public function getSong()
    {
        return Song::find($this->song_id);
    }

    public function getPlaylist()
    {
        return PickAndChoosePlaylist::find($this->playlist_id);
    }
}
