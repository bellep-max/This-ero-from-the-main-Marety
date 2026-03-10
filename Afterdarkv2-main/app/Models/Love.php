<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Love extends MorphPivot
{
    protected $table = 'love';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'loveable_id',
        'loveable_type',
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loveable(): MorphTo
    {
        return $this->morphTo();
    }

    // todo REFACTOR
    //    public function artist()
    //    {
    //        return $this->belongsToMor(Artist::class, 'loveable');
    //    }
    //
    //    public function getMorphObjectAttribute()
    //    {
    //        return new \App\Artist($this);
    //    }
}
