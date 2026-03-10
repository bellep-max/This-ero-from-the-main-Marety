<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 16:54.
 */

namespace App\Models;

use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reaction extends Model
{
    use SanitizedRequest;

    protected $fillable = [
        'user_id',
        'reactionable_id',
        'reactionable_type',
        'type',
    ];

    protected $hidden = [
        'id',
        'updated_at',
        'reactionable_id',
        'reactionable_type',
    ];

    public function reactionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getObjectAttribute()
    {
        return (new $this->reactionable_type)::find($this->reactionable_id);
    }
}
