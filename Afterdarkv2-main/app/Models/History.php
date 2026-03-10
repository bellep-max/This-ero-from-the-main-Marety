<?php

namespace App\Models;

use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class History extends Pivot
{
    use SanitizedRequest;

    protected $table = 'histories';

    protected $fillable = [
        'user_id',
        'historyable_id',
        'historyable_type',
        'ownerable_type',
        'ownerable_id',
        'interaction_count',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function historyable(): MorphTo
    {
        return $this->morphTo();
    }

    public function ownerable(): MorphTo
    {
        return $this->morphTo();
    }
}
