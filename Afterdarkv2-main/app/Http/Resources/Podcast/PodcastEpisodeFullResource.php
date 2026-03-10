<?php

namespace App\Http\Resources\Podcast;

use App\Http\Resources\User\UserShortResource;
use App\Models\Episode;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PodcastEpisodeFullResource extends JsonResource
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
            'favorite' => $this->favorite,
            'created_at' => $this->created_at->format('d.m.Y'),
            'allow_comments' => $this->allow_comments,
            'allow_download' => $this->allow_download,
            'is_visible' => $this->is_visible,
            'explicit' => $this->explicit,
            'user' => $this->whenLoaded('user', UserShortResource::make($this->user)),
            'type' => Episode::class,
        ];
    }
}
