<?php

namespace App\Observers;

use App\Constants\RoleConstants;
use App\Enums\SubscriptionStatusEnum;
use App\Models\MESubscription;

class SubscriptionObserver
{
    /**
     * Handle the activity "created" event.
     */
    public function created(MESubscription $subscription): void
    {
        $subscription->user()->update([
            'group_id' => $subscription->plan->group_id,
            'ends_at' => $subscription->next_billing_date,
        ]);
    }

    /**
     * Handle the activity "updated" event.
     */
    public function updated(MESubscription $subscription): void
    {
        $subscription->user()->update([
            'group_id' => $subscription->status !== SubscriptionStatusEnum::Cancelled
                ? $subscription->plan->group_id
                : RoleConstants::SITE_SUBSCRIPTION,
            'ends_at' => $subscription->status !== SubscriptionStatusEnum::Cancelled
                ? $subscription->next_billing_date
                : null,
        ]);
    }

    /**
     * Handle the activity "deleted" event.
     *
     * @return void
     */
    public function deleted(MESubscription $subscription)
    {
        //
    }

    /**
     * Handle the activity "restored" event.
     *
     * @return void
     */
    public function restored(MESubscription $subscription)
    {
        //
    }

    /**
     * Handle the activity "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(MESubscription $subscription)
    {
        //
    }
}
