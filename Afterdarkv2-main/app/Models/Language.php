<?php

namespace App\Models;

use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use SanitizedRequest;

    protected $table = 'musicengine_languages';

    protected $fillable = [
        'name',
        'language',
    ];

    protected $appends = [
        'artwork',
    ];

    // GETTERS

    public function getArtworkAttribute(): string
    {
        return asset('assets/images/language.png');
    }
}
