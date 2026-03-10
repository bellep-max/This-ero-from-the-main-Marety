<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Channelable extends Pivot
{
    protected $fillable = [
        'channelable_id',
        'channelable_type',
    ];

    public function channelable(): MorphTo
    {
        return $this->morphTo();
    }
}
