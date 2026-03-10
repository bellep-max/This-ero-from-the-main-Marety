<?php

namespace App\Observers;

use App\Models\Activity;
use App\Models\Album;
use Illuminate\Support\Facades\DB;

class AlbumObserver
{
    /**
     * Handle the activity "created" event.
     *
     * @return void
     */
    public function created(Album $album)
    {
        //
    }

    /**
     * Handle the activity "updated" event.
     *
     * @return void
     */
    public function updated(Album $album)
    {
        //
    }

    /**
     * Handle the activity "deleted" event.
     *
     * @return void
     */
    public function deleted(Album $album)
    {
        DB::table('album_songs')->where('album_id', $album->id)->delete();
        $album->comments()->delete();
        $album->loves()->delete();
        $album->activities()->delete();
    }

    /**
     * Handle the activity "restored" event.
     *
     * @return void
     */
    public function restored(Album $album)
    {
        //
    }

    /**
     * Handle the activity "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Album $album)
    {
        //
    }
}
