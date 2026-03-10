<?php

namespace App\Http\Resources\Podcast;

use App\Http\Resources\User\UserShortResource;
use App\Models\Episode;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PodcastEpisodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'artwork' => $this->artwork,
            'duration' => $this->time,
            'path' => $this->path,
            'allow_download' => $this->allow_download,
            'allow_comments' => $this->allow_comments,
            'favorite' => $this->favorite,
            'created_at' => $this->created_at->format('d.m.Y'),
            'total_plays' => $this->play_count,
            'likes' => $this->loves_count,
            'user' => $this->whenLoaded('user', UserShortResource::make($this->user)),
            'type' => Episode::class,
            'is_liked' => $this->when(auth()->check(), auth()->user()?->loves()
                ->where('loveable_id', $this->id)
                ->where('loveable_type', Episode::class)
                ->exists()
            ),
        ];
    }
}
