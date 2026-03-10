<?php

namespace App\Models;

use App\Enums\AdventureSongTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    protected $table = 'tags';

    protected $fillable = [
        'tag',
    ];

    public $timestamps = false;

    public function posts(): MorphToMany
    {
        return $this->morphedByMany(Post::class, 'taggable');
    }

    public function songs(): MorphToMany
    {
        return $this->morphedByMany(Song::class, 'taggable');
    }

    public function podcasts(): MorphToMany
    {
        return $this->morphedByMany(Podcast::class, 'taggable');
    }

    public function adventures(): MorphToMany
    {
        return $this->morphedByMany(Adventure::class, 'taggable')
            ->where('type', AdventureSongTypeEnum::Heading);
    }
}
