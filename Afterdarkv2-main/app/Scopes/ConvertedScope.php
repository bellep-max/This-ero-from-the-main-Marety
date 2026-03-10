<?php

namespace App\Scopes;

use App\Constants\DefaultConstants;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ConvertedScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        //$builder->where($model->getTable() . '.mp3', '=', DefaultConstants::TRUE);
    }
}
