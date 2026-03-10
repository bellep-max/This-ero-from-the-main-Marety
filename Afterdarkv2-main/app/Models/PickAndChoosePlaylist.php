<?php

namespace App\Models;

use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PickAndChoosePlaylist extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SanitizedRequest;

    protected $fillable = [
        'label',
        'desc',
    ];

    public function songs(): HasMany
    {
        return $this->hasMany(PickAndChoosePlaylistSong::class);
    }
}
