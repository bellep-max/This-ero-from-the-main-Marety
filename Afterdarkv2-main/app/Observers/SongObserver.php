<?php

namespace App\Observers;

use App\Enums\ActivityTypeEnum;
use App\Models\Song;
use App\Services\NotificationService;

readonly class SongObserver
{
    public function __construct(private readonly NotificationService $notificationService) {}

    /**
     * Handle the activity "created" event.
     */
    public function created(Song $song): void
    {
        $song->activities()->create([
            'user_id' => auth()->id(),
            'action' => ActivityTypeEnum::addSong,
        ]);

        $this->notificationService->notify(auth()->user()->followers, $song, ActivityTypeEnum::addSong);
    }

    /**
     * Handle the activity "updated" event.
     *
     * @return void
     */
    public function updated(Song $song)
    {
        //
    }

    /**
     * Handle the activity "deleted" event.
     */
    public function deleted(Song $song): void
    {
        $song->comments()->delete();
        $song->loves()->delete();
        $song->tags()->delete();
        $song->collections()->delete();
        $song->activities()->delete();
        $song->reports()->delete();
        $song->notifications()->delete();

        $song->tags()->detach();
        $song->playlists()->detach();
        $song->albums()->detach();
        $song->genres()->detach();
    }

    /**
     * Handle the activity "restored" event.
     *
     * @return void
     */
    public function restored(Song $song)
    {
        //
    }

    /**
     * Handle the activity "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Song $song)
    {
        //
    }
}
