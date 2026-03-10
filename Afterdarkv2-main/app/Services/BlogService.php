<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostTag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BlogService
{
    public function getPostStats(): Collection
    {
        return Post::query()
            ->select(DB::raw('count(*) as count'), 'created_at')
            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->get();
    }

    public function getPostTags(): Collection
    {
        return PostTag::query()
            ->select('tag', DB::raw('count(*) as count'))
            ->groupBy('tag')
            ->orderBy('count', 'desc')
            ->get();
    }

    public function setPostLink(string $string = ''): ?string
    {
        return $string
            ? str_replace('<a ', '<a target="_blank" ', $string)
            : $string;
    }
}
