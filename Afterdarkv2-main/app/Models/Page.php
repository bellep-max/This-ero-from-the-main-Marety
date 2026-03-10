<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-08-01
 * Time: 17:04.
 */

namespace App\Models;

use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    use SanitizedRequest;

    protected $fillable = [
        'user_id',
        'title',
        'alt_name',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
