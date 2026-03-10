<?php

namespace App\Observers;

use App\Models\Event;

class EventObserver
{
    /**
     * Handle the activity "created" event.
     *
     * @return void
     */
    public function created(Event $event)
    {
        //
    }

    /**
     * Handle the activity "updated" event.
     *
     * @return void
     */
    public function updated(Event $event)
    {
        //
    }

    /**
     * Handle the activity "deleted" event.
     *
     * @return void
     */
    public function deleted(Event $event)
    {
        $event->notifications()->delete();
        $event->activities()->delete();
    }

    /**
     * Handle the activity "restored" event.
     *
     * @return void
     */
    public function restored(Event $event)
    {
        //
    }

    /**
     * Handle the activity "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Event $event)
    {
        //
    }
}
