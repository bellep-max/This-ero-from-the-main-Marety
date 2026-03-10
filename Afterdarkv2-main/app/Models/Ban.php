<?php

/**
 * Created by PhpStorm.
 * User: lechchut
 * Date: 7/29/19
 * Time: 1:18 PM.
 */

namespace App\Models;

use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ban extends Model
{
    use SanitizedRequest;

    protected $table = 'bans';

    protected $fillable = [
        'user_id',
        'reason',
        'end_at',
        'ip',
    ];

    protected $casts = [
        'end_at' => 'datetime',
    ];

    // RELATIONS
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
