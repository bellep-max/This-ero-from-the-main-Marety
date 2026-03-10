<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    protected $fillable = [
        'reportable_id',
        'reportable_type',
        'message',
        'type',
    ];

    protected $casts = [
        'type' => 'integer',
    ];

    public function reportable(): MorphTo
    {
        return $this->morphTo()->withoutGlobalScopes();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
