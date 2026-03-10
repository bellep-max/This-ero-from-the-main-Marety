<?php

namespace App\Observers;

use App\Models\Playlist;
use App\Models\PlaylistSong;

class PlaylistObserver
{
    /**
     * Handle the activity "created" event.
     *
     * @return void
     */
    public function created(Playlist $playlist)
    {
        //
    }

    /**
     * Handle the activity "updated" event.
     *
     * @return void
     */
    public function updated(Playlist $playlist)
    {
        //
    }

    /**
     * Handle the activity "deleted" event.
     *
     * @return void
     */
    public function deleted(Playlist $playlist)
    {
        $playlist->notifications()->delete();
        $playlist->activities()->delete();
        $playlist->loves()->delete();
        $playlist->comments()->delete();
        $playlist->collaboratorsTest()->detach();

        PlaylistSong::query()->where('playlist_id', $playlist->id)->delete();
    }

    /**
     * Handle the activity "restored" event.
     *
     * @return void
     */
    public function restored(Playlist $playlist)
    {
        //
    }

    /**
     * Handle the activity "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Playlist $playlist)
    {
        //
    }
}
