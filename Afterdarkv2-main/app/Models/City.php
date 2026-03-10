<?php

namespace App\Models;

use App\Traits\ArtworkTrait;
use App\Traits\ImageMediaTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;

class City extends Model implements HasMedia
{
    use ArtworkTrait;
    use ImageMediaTrait;

    protected $table = 'musicengine_cities';

    protected $fillable = [
        'name',
        'country_id',
        'country_code',
        'district',
        'fixed',
        'is_visible',
    ];

    protected $appends = ['artwork'];

    // RELATIONS
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
