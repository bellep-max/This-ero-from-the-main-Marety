<?php

namespace App\Listeners;

use App\Enums\ActivityTypeEnum;
use App\Events\RemovedTrackFromPlaylist;
use App\Models\Activity;
use App\Models\User;

class CreateRemovedTrackFromPlaylistActivity
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RemovedTrackFromPlaylist $event): void
    {
        $activity = Activity::create([
            'action' => ActivityTypeEnum::removeFromPlaylist,
            'activityable_id' => $event->playlist->id,
            'activityable_type' => $event->playlist->getMorphClass(),
            'hostable_id' => auth()->id(),
            'hostable_type' => User::class,
        ]);

        $activity->actionable()
            ->create([
                'actionable_id' => $event->song->id,
                'actionable_type' => $event->song->getMorphClass(),
            ]);
    }
}
