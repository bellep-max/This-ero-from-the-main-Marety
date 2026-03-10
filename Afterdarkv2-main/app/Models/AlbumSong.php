<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AlbumSong extends Pivot
{
    protected $table = 'album_songs';

    protected $fillable = [
        'song_id',
        'album_id',
        'priority',
    ];

    // RELATIONS
    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }
}
