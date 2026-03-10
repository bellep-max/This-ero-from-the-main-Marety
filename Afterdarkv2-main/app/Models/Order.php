<?php

namespace App\Models;

use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SanitizedRequest;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'orderable_id',
        'orderable_type',
        'payment',
        'amount',
        'commission',
        'currency',
        'payment_status',
        'transaction_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderable(): MorphTo
    {
        return $this->morphTo();
    }

    // todo
    //    public function transaction(): BelongsTo
    //    {
    //        return $this->belongsTo(Transaction::class);
    //    }

    public function getObjectAttribute()
    {
        return (new $this->orderable_type)::find($this->orderable_id);
    }
}
