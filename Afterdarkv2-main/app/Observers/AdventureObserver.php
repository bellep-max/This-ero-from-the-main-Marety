<?php

namespace App\Observers;

use App\Models\Adventure;

class AdventureObserver
{
    /**
     * Handle the activity "created" event.
     *
     * @return void
     */
    public function created(Adventure $adventure)
    {
        //
    }

    /**
     * Handle the activity "updated" event.
     */
    public function updated(Adventure $adventure): void
    {
        //
    }

    /**
     * Handle the activity "deleted" event.
     *
     * @return void
     */
    public function deleted(Adventure $adventure)
    {
        $adventure->media()->delete();
    }

    /**
     * Handle the activity "restored" event.
     *
     * @return void
     */
    public function restored(Adventure $adventure)
    {
        //
    }

    /**
     * Handle the activity "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Adventure $adventure)
    {
        //
    }
}
