<?php

namespace App\Observers;

use App\Models\Activity;
use App\Models\Notification;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the activity "creating" event.
     */
    public function creating(User $user): void
    {
        $user->last_seen_notif = now();
    }

    /**
     * Handle the activity "created" event.
     *
     * @return void
     */
    public function created(User $user)
    {
        //
    }

    /**
     * Handle the activity "updated" event.
     *
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Handle the activity "deleted" event.
     *
     * @return void
     */
    public function deleted(User $user)
    {
        $relations = [
            'loves', 'ban', 'connects', 'media', 'playlists', 'tracks', 'subscriptions', 'userSubscriptions', 'comments', 'activities', 'history',
        ];

        foreach ($relations as $relation) {
            $user->$relation()->delete();
        }

        $user->notifications()->each(function ($notification) {
            $notification->delete();
        });

        Notification::query()->where('notificationable_type', User::class)->where('notificationable_id', $user->id)->delete();
        Notification::query()->where('user_id', $user->id)->delete();
        Activity::query()->where('activityable_type', User::class)->where('activityable_id', $user->id)->delete();
        Activity::query()->where('user_id', $user->id)->delete();
    }

    /**
     * Handle the activity "restored" event.
     *
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the activity "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
