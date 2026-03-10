<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class HashTag extends Model
{
    protected $table = 'hash_tags';

    protected $fillable = [
        'hashable_id',
        'hashable_type',
        'tag',
    ];

    public function hashable(): MorphTo
    {
        return $this->morphTo();
    }
}
