<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SongLog extends Model
{
    protected $table = 'song_spotify_logs';

    protected $fillable = [
        'artwork',
        'preview_url',
        'song_id',
        'spotify_id',
        'youtube',
    ];

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }
}
