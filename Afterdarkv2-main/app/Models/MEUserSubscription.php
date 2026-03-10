<?php

namespace App\Models;

use App\Enums\SubscriptionStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class MEUserSubscription extends Pivot
{
    use SoftDeletes;

    public $table = 'musicengine_user_subscriptions';

    protected $fillable = [
        'amount',
        'currency',
        'deleted_at',
        'last_payment_date',
        'next_billing_date',
        'plan_id',
        'status',
        'subscribed_user_id',
        'subscription_id',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'double',
        'last_payment_date' => 'date',
        'next_billing_date' => 'date',
        'status' => SubscriptionStatusEnum::class,
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(MEPlan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscribedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subscribed_user_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(MESubscription::class);
    }
}
