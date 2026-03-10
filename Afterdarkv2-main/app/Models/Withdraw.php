<?php

namespace App\Models;

use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdraw extends Model
{
    use SanitizedRequest;

    protected $fillable = [
        'user_id',
        'amount',
        'paid',
    ];

    protected $dates = [
        'created_at',
    ];

    protected static function booted()
    {
        static::created(function ($model) {
            auth()->user()->decrement('balance', $model->amount);
        });

        static::deleting(function ($model) {
            auth()->user()->increment('balance', $model->amount);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
