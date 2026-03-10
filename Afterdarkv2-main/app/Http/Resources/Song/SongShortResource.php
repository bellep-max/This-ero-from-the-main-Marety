<?php

namespace App\Http\Resources\Song;

use App\Http\Resources\TagResource;
use App\Http\Resources\User\UserShortResource;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SongShortResource extends JsonResource
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
            'path' => $this->path,
            'stream_url' => $this->stream_url,
            'streamable' => $this->streamable,
            'duration' => $this->minutes,
            'favorite' => $this->favorite,
            'hd' => $this->hd,
            'is_patron' => $this->is_patron,
            'user' => UserShortResource::make($this->user),
            'tags' => $this->whenLoaded('tags', TagResource::collection($this->tags)),
            'type' => Song::class,
            'is_liked' => $this->when(auth()->check(), auth()->user()?->loves()
                ->where('loveable_id', $this->id)
                ->where('loveable_type', Song::class)
                ->exists()
            ),
        ];
    }
}
