<?php

namespace App\Models;

use App\Traits\ArtworkTrait;
use App\Traits\ImageMediaTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;

class CountryLanguage extends Model implements HasMedia
{
    use ArtworkTrait;
    use ImageMediaTrait;

    protected $table = 'musicengine_country_languages';

    protected $fillable = [
        'country_id',
        'country_code',
        'name',
        'is_official',
        'fixed',
        'is_visible',
    ];

    protected $appends = [
        'artwork',
    ];

    protected $casts = [
        'fixed' => 'boolean',
        'is_visible' => 'boolean',
        'is_official' => 'boolean',
    ];

    // RELATIONS
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    // SCOPES
    public function scopeFixed($query)
    {
        return $query->where('fixed', true);
    }
}
