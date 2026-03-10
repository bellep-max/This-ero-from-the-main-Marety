<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Torann\LaravelMetaTags\Facades\MetaTag;

class MetatagService
{
    public function setMetatags(Model $model): void
    {
        MetaTag::set('title', $model->meta_title ?: $model->name);
        MetaTag::set('description', $model->meta_description ?: $model->description);
        MetaTag::set('keywords', $model->meta_keywords);

        if ($model->artwork) {
            MetaTag::set('image', $model->artwork);
        }
    }
}
