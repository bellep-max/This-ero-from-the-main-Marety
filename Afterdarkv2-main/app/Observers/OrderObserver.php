<?php

namespace App\Observers;

use App\Models\Group;
use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the activity "created" event.
     */
    public function created(Order $order): void
    {
        $model = (new $order->orderable_type)->findOrFail($order->orderable_id);

        if ($model->user_id) {
            $commission = (intval(Group::getUserValue('monetization_sale_cut', $model->user_id)) * $order->amount) / 100;
            $model->user()->increment('balance', $commission);

            $order->update([
                'commission' => $commission,
            ]);
        }
    }

    /**
     * Handle the activity "updated" event.
     *
     * @return void
     */
    public function updated(Order $order)
    {
        //
    }

    /**
     * Handle the activity "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the activity "restored" event.
     *
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the activity "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }
}
