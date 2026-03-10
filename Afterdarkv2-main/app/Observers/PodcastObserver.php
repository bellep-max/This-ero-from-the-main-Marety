<?php

namespace App\Observers;

use App\Models\Podcast;

class PodcastObserver
{
    /**
     * Handle the activity "created" event.
     *
     * @return void
     */
    public function created(Podcast $podcast)
    {
        //
    }

    /**
     * Handle the activity "updated" event.
     *
     * @return void
     */
    public function updated(Podcast $podcast)
    {
        //
    }

    /**
     * Handle the activity "deleted" event.
     *
     * @return void
     */
    public function deleted(Podcast $podcast)
    {
        $relations = ['episodes', 'notifications', 'activities', 'loves', 'comments'];

        foreach ($relations as $relation) {
            if ($podcast->$relation()->exists()) {
                $podcast->$relation()->delete();
            }
        }
    }

    /**
     * Handle the activity "restored" event.
     *
     * @return void
     */
    public function restored(Podcast $podcast)
    {
        //
    }

    /**
     * Handle the activity "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Podcast $podcast)
    {
        //
    }
}
