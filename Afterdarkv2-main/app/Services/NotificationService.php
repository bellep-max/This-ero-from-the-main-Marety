<?php

namespace App\Services;

use App\Enums\ActivityTypeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    public function notify(Collection|\Illuminate\Support\Collection|User|null $users, Model $model, ActivityTypeEnum $activity): void
    {
        if ($users instanceof User) {
            $users = collect([$users]);
        }

        $users?->each(function (User $user) use ($activity, $model) {
            $user->notificationsTest()->create([
                'action' => $activity,
                'notificationable_id' => $model->id,
                'notificationable_type' => $model->getMorphClass(),
                'hostable_id' => auth()->id(),
                'hostable_type' => User::class,
            ]);
        });
    }

    public function markAsRead(Model $model): void
    {
        auth()->user()
            ->unreadNotifications()
            ->where('notificationable_id', $model->id)
            ->where('notificationable_type', $model->getMorphClass())
            ->update(['read_at' => now()]);
    }
}
