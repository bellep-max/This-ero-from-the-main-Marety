<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PickAndChoosePlaylistSong extends Model
{
    protected $table = 'pick_and_choose_playlist_song';

    protected $fillable = [
        'song_id',
        'pick_and_choose_playlist_id',
    ];

    public function song(): HasOne
    {
        return $this->hasOne(Song::class, 'id', 'song_id');
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'pick_and_choose_playlist_song_childrens', 'song_id', 'child_id');
    }

    public function playlist(): BelongsTo
    {
        return $this->belongsTo(PickAndChoosePlaylist::class, 'pick_and_choose_playlist_id', 'id');
    }
}
