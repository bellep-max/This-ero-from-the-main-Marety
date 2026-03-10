<?php

namespace App\Models;

use App\Constants\DefaultConstants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdventureProperty extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'adventure_id',
        'is_visible',
        'allow_comments',
        'approved',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'allow_comments' => 'boolean',
        'approved' => 'boolean',
    ];

    public function adventure(): BelongsTo
    {
        return $this->belongsTo(Adventure::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', DefaultConstants::TRUE);
    }
}
