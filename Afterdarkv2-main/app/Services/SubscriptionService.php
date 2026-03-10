<?php

namespace App\Services;

use App\Enums\SubscriptionStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SubscriptionService
{
    //    public function checkIfSubscribed(User $subscriber, int $subscribedToId): bool
    //    {
    //
    //    }

    public static function getActiveUserSubscription(User $subscriber, int $subscribedToId, bool $showSuspended = true): ?Model
    {
        return $subscriber->userSubscriptions()
            ->where('subscribed_user_id', $subscribedToId)
            ->where(function ($query) use ($showSuspended) {
                $query->where('status', SubscriptionStatusEnum::Active)
                    ->when($showSuspended, function ($query) {
                        $query->orWhere('status', SubscriptionStatusEnum::Suspended);
                    });
            })
            ->first();
    }
}
