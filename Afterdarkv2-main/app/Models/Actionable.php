<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Actionable extends Pivot
{
    protected $table = 'actionables';

    public $timestamps = false;

    protected $fillable = [
        'activity_id',
        'actionable_id',
        'actionable_type',
    ];

    public function actionable(): MorphTo
    {
        return $this->morphTo();
    }
}
