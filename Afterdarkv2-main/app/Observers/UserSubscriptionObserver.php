<?php

namespace App\Observers;

use App\Constants\RoleConstants;
use App\Constants\StatusConstants;
use App\Models\MEUserSubscription;

class UserSubscriptionObserver
{
    /**
     * Handle the activity "created" event.
     */
    public function created(MEUserSubscription $userSubscription): void
    {
        $userSubscription->user->update([
            'group_id' => $userSubscription->plan->role_id,
            'ends_at' => $userSubscription->next_billing_date,
        ]);
    }

    /**
     * Handle the activity "updated" event.
     */
    public function updated(MEUserSubscription $userSubscription): void
    {
        $userSubscription->user->update([
            'group_id' => $userSubscription->status !== StatusConstants::CANCELLED
                ? $userSubscription->plan->role_id
                : RoleConstants::USER_SUBSCRIPTION,
            'ends_at' => $userSubscription->status !== StatusConstants::CANCELLED
                ? $userSubscription->next_billing_date
                : null,
        ]);
    }

    /**
     * Handle the activity "deleted" event.
     *
     * @return void
     */
    public function deleted(MEUserSubscription $userSubscription)
    {
        //
    }

    /**
     * Handle the activity "restored" event.
     *
     * @return void
     */
    public function restored(MEUserSubscription $userSubscription)
    {
        //
    }

    /**
     * Handle the activity "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(MEUserSubscription $userSubscription)
    {
        //
    }
}
