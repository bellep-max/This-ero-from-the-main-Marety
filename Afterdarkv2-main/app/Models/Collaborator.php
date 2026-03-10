<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Collaborator extends Pivot
{
    protected $table = 'collaborators';

    protected $fillable = [
        'user_id',
        'playlist_id',
        'friend_id',
        'approved',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function playlist(): BelongsTo
    {
        return $this->belongsTo(Playlist::class);
    }

    public function friend(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
