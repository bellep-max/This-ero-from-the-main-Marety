<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-26
 * Time: 10:56.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Stream extends Model
{
    protected $table = 'stream_stats';

    protected $fillable = [
        'ip',
        'revenue',
        'streamable_id',
        'streamable_type',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function streamable(): MorphTo
    {
        return $this->morphTo();
    }
}
