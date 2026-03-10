<?php

namespace App\Models\Subscriptions;

use LucasDotVin\Soulbscription\Models\Feature as SoulBFeature;

class Feature extends SoulbFeature
{
    //
    protected $table = 'subscr_features';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        // Merge additional fields into the parent's fillable array
        $this->mergeFillable([
            'label',
            'description',
        ]);
    }

    public function plans()
    {
        return $this->belongsToMany(config('soulbscription.models.plan'), 'subscr_feature_plan')
            ->using(config('soulbscription.models.feature_plan'));
    }

}
