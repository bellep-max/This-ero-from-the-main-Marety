<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MEPlan extends Model
{
    protected $table = 'musicengine_plans';

    protected $fillable = [
        'type',
        'paypal_id',
        'group_id',
        'name',
        'description',
        'price',
        'percentage',
        'interval',
        'status',
    ];

    protected $casts = [
        'price' => 'float',
        'percentage' => 'int',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
