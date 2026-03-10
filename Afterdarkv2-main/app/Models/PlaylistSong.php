<?php

namespace App\Models;

use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PlaylistSong extends Pivot
{
    use SanitizedRequest;

    protected $table = 'playlist_songs';

    protected $fillable = [
        'playlist_id',
        'priority',
        'song_id',
    ];

    protected $casts = [
        'priority' => 'integer',
    ];

    public function song(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function playlist(): BelongsTo
    {
        return $this->belongsTo(Playlist::class);
    }
}
