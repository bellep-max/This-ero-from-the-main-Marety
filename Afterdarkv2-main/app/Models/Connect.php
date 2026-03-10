<?php

namespace App\Models;

use App\Constants\ProviderConstants;
use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Connect extends Model
{
    use SanitizedRequest;

    protected $table = 'oauth_socialite';

    protected $fillable = [
        'user_id',
        'provider_id',
        'provider_name',
        'provider_email',
        'provider_artwork',
        'service',
        'autopost',
    ];

    protected $casts = [
        'autopost' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSpotify($query)
    {
        return $query->where('service', ProviderConstants::SPOTIFY);
    }
}
