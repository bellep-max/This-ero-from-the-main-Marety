<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArtistLog extends Model
{
    protected $table = 'artist_spotify_logs';

    protected static function boot(): void
    {
        parent::boot();
    }
}
