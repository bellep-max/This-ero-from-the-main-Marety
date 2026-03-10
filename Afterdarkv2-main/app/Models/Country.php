<?php

namespace App\Models;

use App\Traits\ArtworkTrait;
use App\Traits\ImageMediaTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;

class Country extends Model implements HasMedia
{
    use ArtworkTrait;
    use ImageMediaTrait;

    protected $table = 'musicengine_countries';

    protected $fillable = [
        'code',
        'name',
        'continent',
        'region_id',
        'local_name',
        'government_form',
        'code2',
        'fixed',
        'is_visible',
    ];

    protected $appends = [
        'artwork',
    ];

    protected $casts = [
        'fixed' => 'boolean',
        'is_visible' => 'boolean',
    ];

    // RELATIONS
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'country_id');
    }

    // SCOPES
    public function scopeFixed($query)
    {
        return $query->where('fixed', true);
    }
}
