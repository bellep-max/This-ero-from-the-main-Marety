<?php

namespace App\Models;

use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use SanitizedRequest;

    protected $fillable = [
        'active',
        'description',
        'plan_period',
        'plan_period_format',
        'price',
        'role_id',
        'title',
        'trial',
        'trial_period',
        'trial_period_format',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
