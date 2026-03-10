<?php

namespace App\Observers;

use App\Models\Station;

class StationObserver
{
    /**
     * Handle the activity "created" event.
     *
     * @return void
     */
    public function created(Station $station)
    {
        //
    }

    /**
     * Handle the activity "updated" event.
     *
     * @return void
     */
    public function updated(Station $station)
    {
        //
    }

    /**
     * Handle the activity "deleted" event.
     *
     * @return void
     */
    public function deleted(Station $station)
    {
        $station->comments()->delete();
        $station->loves()->delete();
        $station->activities()->delete();
        $station->notifications()->delete();
    }

    /**
     * Handle the activity "restored" event.
     *
     * @return void
     */
    public function restored(Station $station)
    {
        //
    }

    /**
     * Handle the activity "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Station $station)
    {
        //
    }
}
