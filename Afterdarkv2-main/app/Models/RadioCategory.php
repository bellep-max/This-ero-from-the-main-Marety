<?php

namespace App\Models;

use App\Traits\ArtworkTrait;
use App\Traits\ImageMediaTrait;
use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

class RadioCategory extends Model implements HasMedia
{
    use ArtworkTrait;
    use ImageMediaTrait;
    use SanitizedRequest;

    protected $table = 'radio_categories';

    protected $fillable = [
        'alt_name',
        'description',
        'meta_description',
        'meta_keywords',
        'meta_title',
        'name',
        'parent_id',
        'priority',
    ];

    protected $appends = [
        'slug',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function getSlugAttribute(): string
    {
        return Str::slug($this->alt_name) ?: 'no-slug';
    }

    public function getPermalinkAttribute(): string
    {
        return route('radio.browse.category', $this->slug);
    }
}
