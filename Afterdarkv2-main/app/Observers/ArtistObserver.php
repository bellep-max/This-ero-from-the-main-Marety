<?php

namespace App\Observers;

use App\Models\Artist;

class ArtistObserver
{
    /**
     * Handle the activity "created" event.
     *
     * @return void
     */
    public function created(Artist $artist)
    {
        //
    }

    /**
     * Handle the activity "updated" event.
     *
     * @return void
     */
    public function updated(Artist $artist)
    {
        //
    }

    /**
     * Handle the activity "deleted" event.
     *
     * @return void
     */
    public function deleted(Artist $artist)
    {
        $artist->comments()->delete();
        $artist->loves()->delete();
        $artist->users()->update([
            'artist_id' => null,
        ]);
        $artist->requests()->delete();
        $artist->activitiesTest()->delete();
        $artist->podcasts()->delete();
    }

    /**
     * Handle the activity "restored" event.
     *
     * @return void
     */
    public function restored(Artist $artist)
    {
        //
    }

    /**
     * Handle the activity "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Artist $artist)
    {
        //
    }
}
