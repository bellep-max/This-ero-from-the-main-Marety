<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SongTag extends Model
{
    protected $table = 'song_tags';

    protected $fillable = [
        'song_id',
        'tag',
    ];

    public $timestamps = false;

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }

    public function songs(): Builder
    {
        return Song::query()
            ->with([
                'user',
                'tags',
            ])
            ->leftJoin('song_tags', 'song_tags.song_id', '=', (new Song)->getTable() . '.id')
            ->select('songs.*', 'song_tags.id as host_id')
            ->where('song_tags.tag', $this->tag);
    }

    //    public function getPermalinkAttribute(): string
    //    {
    //        if (! $slug = str_slug($this->tag)) {
    //            return route('homepage');
    //        }
    //
    //        return route('tag', $slug);
    //    }
}
