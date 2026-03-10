<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;

class TagService
{
    public static function setModelTags(Model $model, array $tags): void
    {
        if (!$tags) {
            return;
        }

        $tagIds = [];

        foreach ($tags as $tag) {
            if ($tag['id'] < 0) {
                $newTag = Tag::updateOrCreate([
                    'tag' => $tag['tag'],
                ]);

                $tagIds[] = $newTag->id;
            } else {
                $tagIds[] = $tag['id'];
            }
        }

        $model->tags()->sync($tagIds);
    }
}
