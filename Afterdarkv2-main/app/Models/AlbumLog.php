<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlbumLog extends Model
{
    protected $table = 'album_spotify_logs';

    protected static function boot(): void
    {
        parent::boot();
    }
}
