<?php

namespace App\Http\Resources\Song;

use App\Http\Resources\Genre\GenreShortResource;
use App\Http\Resources\SlideResource;
use App\Http\Resources\TagResource;
use App\Http\Resources\User\UserShortResource;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SongFullResource extends JsonResource
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
            'allow_comments' => $this->allow_comments,
            'allow_download' => $this->allow_download,
            'is_visible' => $this->is_visible,
            'is_explicit' => $this->is_explicit,
            'hd' => $this->hd,
            'created_at' => $this->created_at->format('d.m.Y'),
            'is_patron' => $this->is_patron,
            'duration' => $this->minutes,
            'user' => $this->whenLoaded('user', UserShortResource::make($this->user)),
            'tags' => $this->whenLoaded('tags', TagResource::collection($this->tags)),
            'genres' => $this->whenLoaded('genres', GenreShortResource::collection($this->genres)),
            'slides' => $this->whenLoaded('slides', SlideResource::collection($this->slides)),
            'likes' => $this->whenHas('fans_count', $this->fans_count),
            'type' => Song::class,
            'is_liked' => $this->when(auth()->check(), auth()->user()?->loves()
                ->where('loveable_id', $this->id)
                ->where('loveable_type', Song::class)
                ->exists()
            ),
        ];
    }
}
