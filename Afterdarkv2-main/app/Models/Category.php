<?php

namespace App\Models;

use App\Traits\ArtworkTrait;
use App\Traits\ImageMediaTrait;
use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;

class Category extends Model implements HasMedia
{
    use ArtworkTrait;
    use ImageMediaTrait;
    use SanitizedRequest;

    protected $fillable = [
        'parent_id',
        'posi',
        'name',
        'alt_name',
        'description',
        'news_sort',
        'news_msort',
        'news_number',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'show_sub',
        'allow_rss',
        'disable_search',
        'disable_main',
        'disable_comments',
        'artworkId',
    ];

    // RELATIONS
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
