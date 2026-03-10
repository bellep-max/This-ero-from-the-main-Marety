<?php

namespace App\Models\Subscriptions;

use Illuminate\Database\Eloquent\Model;
use LucasDotVin\Soulbscription\Models\FeatureConsumption as SoulbFeatureConsumption;

class FeatureConsumption extends SoulbFeatureConsumption
{
    //
    protected $table = 'subscr_feature_consumptions';
}
