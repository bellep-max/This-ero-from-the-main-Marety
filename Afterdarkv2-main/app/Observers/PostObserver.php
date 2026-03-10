<?php

namespace App\Observers;

use App\Models\Post;

class PostObserver
{
    /**
     * Handle the activity "created" event.
     *
     * @return void
     */
    public function created(Post $post)
    {
        //
    }

    /**
     * Handle the activity "updated" event.
     *
     * @return void
     */
    public function updated(Post $post)
    {
        //
    }

    /**
     * Handle the activity "deleted" event.
     *
     * @return void
     */
    public function deleted(Post $post)
    {
        $post->notifications()->delete();
        $post->activities()->delete();
        $post->comments()->delete();
    }

    /**
     * Handle the activity "restored" event.
     *
     * @return void
     */
    public function restored(Post $post)
    {
        //
    }

    /**
     * Handle the activity "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Post $post)
    {
        //
    }
}
