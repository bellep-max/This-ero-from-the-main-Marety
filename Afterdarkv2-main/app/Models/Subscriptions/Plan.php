<?php

namespace App\Models\Subscriptions;

use LucasDotVin\Soulbscription\Models\Plan as SoulbPlan;

class Plan extends SoulbPlan
{
    //
    protected $table = 'subscr_plans';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        // Merge additional fields into the parent's fillable array
        $this->mergeFillable([
            'price',
            'is_subscribable',
            'is_cancelable',
            'is_visible',
            'currency',
            'description',
        ]);
    }

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'is_subscribable' => 'boolean',
            'is_cancelable' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    public function features()
    {
        return $this->belongsToMany(config('soulbscription.models.feature'), 'subscr_feature_plan')
            ->using(config('soulbscription.models.feature_plan'))
            ->withPivot(['charges']);
    }

}
