<?php

namespace App\Models;

use App\Traits\ArtworkTrait;
use App\Traits\ImageMediaTrait;
use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class Meta extends Model implements HasMedia
{
    use ArtworkTrait;
    use ImageMediaTrait;
    use SanitizedRequest;

    protected $table = 'metatags';

    protected $fillable = [
        'priority',
        'url',
        'info',
        'page_title',
        'page_description',
        'page_keywords',
        'auto_keyword',
    ];

    protected $appends = [
        'artwork',
    ];
}
