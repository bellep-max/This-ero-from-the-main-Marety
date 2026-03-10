<?php

namespace App\Observers;

use App\Enums\ActivityTypeEnum;
use App\Models\Artist;
use App\Models\Love;
use App\Models\Notification;
use App\Models\Playlist;
use App\Models\Podcast;
use App\Models\Song;
use App\Models\User;

class LoveObserver
{
    /**
     * Handle the activity "created" event.
     */
    public function created(Love $love): void
    {
        $messageType = match ($love->loveable_type) {
            Song::class => ActivityTypeEnum::favoriteSong,
            User::class => ActivityTypeEnum::followUser,
            Playlist::class => ActivityTypeEnum::followPlaylist,
            Artist::class => ActivityTypeEnum::followArtist,
            Podcast::class => ActivityTypeEnum::followPodcast,
            default => ActivityTypeEnum::default,
        };

        Notification::create([
            'user_id' => $love->loveable_type === User::class
                ? $love->loveable->id
                : $love->loveable->user_id,
            'notificationable_id' => $love->loveable_id,
            'notificationable_type' => $love->loveable_type,
            'action' => $messageType,
            'hostable_id' => auth()->id(),
            'hostable_type' => User::class,
        ]);
    }

    /**
     * Handle the activity "updated" event.
     *
     * @return void
     */
    public function updated(Love $love)
    {
        //
    }

    /**
     * Handle the activity "deleted" event.
     *
     * @return void
     */
    public function deleted(Love $love)
    {
        //
    }

    /**
     * Handle the activity "restored" event.
     *
     * @return void
     */
    public function restored(Love $love)
    {
        //
    }

    /**
     * Handle the activity "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Love $love)
    {
        //
    }
}
