<?php

namespace App\Models;

use App\Enums\SubscriptionStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MESubscription extends Model
{
    use SoftDeletes;

    protected $table = 'musicengine_subscriptions';

    protected $fillable = [
        'user_id',
        'plan_id',
        'subscription_id',
        'status',
        'last_payment_date',
        'next_billing_date',
        'amount',
        'currency',
        'deleted_at',
    ];

    protected $casts = [
        'amount' => 'double',
        'last_payment_date' => 'datetime',
        'next_billing_date' => 'datetime',
        'status' => SubscriptionStatusEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(MEPlan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', SubscriptionStatusEnum::Active);
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', SubscriptionStatusEnum::Suspended);
    }

    public function scopeByStatus($query, $statusList)
    {
        return $query->whereIn('status', $statusList);
    }
}
