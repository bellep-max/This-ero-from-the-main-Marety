<?php

namespace App\Listeners;

use App\Enums\ActivityTypeEnum;
use App\Events\AddedTrackToPlaylist;
use App\Services\NotificationService;

readonly class SendAddTrackToPlaylistNotification
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly NotificationService $notificationService) {}

    /**
     * Handle the event.
     */
    public function handle(AddedTrackToPlaylist $event): void
    {
        $users = collect([
            $event->playlist->followers,
            $event->playlist->collaborators,
            auth()->user()->followers,
        ])->flatten()
            ->filter()
            ->unique();

        if (auth()->id() !== $event->playlist->user_id) {
            $users->add($event->playlist->user);

            if ($users->contains(auth()->user())) {
                // Remove the current user from the notification list
                // to avoid sending a notification to themselves
                $users->forget(auth()->id());
            }
        }

        $this->notificationService->notify($users, $event->playlist, ActivityTypeEnum::addToPlaylist);
    }
}
