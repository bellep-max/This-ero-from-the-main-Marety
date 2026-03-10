<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-24
 * Time: 13:24.
 */

namespace App\Models;

use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtistRequest extends Model
{
    use SanitizedRequest;

    protected $table = 'artist_requests';

    protected $fillable = [
        'user_id',
        'artist_id',
        'artist_name',
        'phone',
        'ext',
        'affiliation',
        'message',
        'facebook',
        'twitter',
        'approved',
    ];

    // RELATIONS
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }
}
