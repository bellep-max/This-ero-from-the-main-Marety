<?php

namespace App\Http\Resources\Adventure;

use App\Http\Resources\Genre\GenreShortResource;
use App\Http\Resources\TagResource;
use App\Http\Resources\User\UserShortResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdventureResource extends JsonResource
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
            'duration' => $this->minutes,
            'roots' => $this->roots()->count(),
            'tags' => $this->whenLoaded('tags', TagResource::collection($this->tags)),
            'genres' => $this->whenLoaded('genres', GenreShortResource::collection($this->genres)),
            'user' => $this->whenLoaded('user', UserShortResource::make($this->user)),
        ];
    }
}
