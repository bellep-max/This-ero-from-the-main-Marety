<?php

namespace App\Observers;

use App\Models\Activity;
use App\Models\Comment;
use App\Models\Notification;

class ActivityObserver
{
    /**
     * Handle the activity "created" event.
     *
     * @return void
     */
    public function created(Activity $activity)
    {
        //
    }

    /**
     * Handle the activity "updated" event.
     *
     * @return void
     */
    public function updated(Activity $activity)
    {
        //
    }

    /**
     * Handle the activity "deleted" event.
     */
    public function deleted(Activity $activity): void
    {
        Comment::query()
            ->where('commentable_type', $activity->getMorphClass())
            ->where('commentable_id', $activity->id)
            ->delete();
        Notification::query()
            ->where('notificationable_type', $activity->getMorphClass())
            ->where('notificationable_id', $activity->id)
            ->delete();
    }

    /**
     * Handle the activity "restored" event.
     *
     * @return void
     */
    public function restored(Activity $activity)
    {
        //
    }

    /**
     * Handle the activity "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Activity $activity)
    {
        //
    }
}
