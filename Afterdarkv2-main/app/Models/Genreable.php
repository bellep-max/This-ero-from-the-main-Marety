<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Genreable extends Pivot
{
    protected $fillable = [
        'genreable_id',
        'genreable_type',
        'genre_id',
    ];

    public function genreable(): MorphTo
    {
        return $this->morphTo();
    }
}
