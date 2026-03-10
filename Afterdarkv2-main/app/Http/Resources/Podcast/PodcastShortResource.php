<?php

namespace App\Http\Resources\Podcast;

use App\Http\Resources\User\UserShortResource;
use App\Models\Podcast;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PodcastShortResource extends JsonResource
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
            'favorite' => $this->favorite,
            'artwork' => $this->artwork,
            'seasons' => $this->seasons,
            'details' => $this->details,
            'created_at' => $this->created_at->format('d.m.Y'),
            'user' => $this->whenLoaded('user', UserShortResource::make($this->user)),
            'type' => Podcast::class,
        ];
    }
}
