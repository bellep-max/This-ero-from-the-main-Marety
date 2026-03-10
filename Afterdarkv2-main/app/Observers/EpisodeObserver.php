<?php

namespace App\Observers;

use App\Enums\ActivityTypeEnum;
use App\Models\Activity;
use App\Models\Episode;
use App\Services\NotificationService;

class EpisodeObserver
{
    public function __construct(private readonly NotificationService $notificationService) {}

    /**
     * Handle the activity "created" event.
     */
    public function created(Episode $episode): void
    {
        $episode->activities()->create([
            'user_id' => auth()->id(),
            'type' => ActivityTypeEnum::addEpisode,
        ]);

        $this->notificationService->notify(auth()->user()->followers, $episode, ActivityTypeEnum::addEpisode);
    }

    /**
     * Handle the activity "updated" event.
     *
     * @return void
     */
    public function updated(Episode $episode)
    {
        //
    }

    /**
     * Handle the activity "deleted" event.
     *
     * @return void
     */
    public function deleted(Episode $episode)
    {
        $episode->comments()->delete();
        $episode->loves()->delete();
        $episode->notifications()->delete();
        $episode->activities()->delete();
    }

    /**
     * Handle the activity "restored" event.
     *
     * @return void
     */
    public function restored(Episode $episode)
    {
        //
    }

    /**
     * Handle the activity "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Episode $episode)
    {
        //
    }
}
