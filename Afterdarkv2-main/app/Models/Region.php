<?php

namespace App\Models;

use App\Scopes\VisibilityScope;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'musicengine_regions';

    protected $fillable = [
        'alt_name',
        'fixed',
        'name',
        'is_visible',
    ];

    protected $appends = [
        'artwork',
    ];

    protected $casts = [
        'fixed' => 'boolean',
        'is_visible' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new VisibilityScope);
    }

    // GETTERS

    public function getArtworkAttribute(): string
    {
        return asset('assets/images/country.png');
    }

    // SCOPES
    public function scopeFixed($query)
    {
        return $query->where('fixed', true);
    }
}
