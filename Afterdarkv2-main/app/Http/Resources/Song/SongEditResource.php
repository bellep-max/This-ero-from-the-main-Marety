<?php

namespace App\Http\Resources\Song;

use App\Http\Resources\OptionResource;
use App\Http\Resources\TagResource;
use App\Http\Resources\VocalOptionResource;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SongEditResource extends JsonResource
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
            'vocal_id' => $this->vocal_id,
            'artwork' => $this->artwork,
            'allow_comments' => $this->allow_comments,
            'allow_download' => $this->allow_download,
            'is_patron' => $this->is_patron,
            'is_visible' => $this->is_visible,
            'is_explicit' => $this->is_explicit,
            'script' => $this->script,
            'tags' => $this->whenLoaded('tags', TagResource::collection($this->tags)),
            'genres' => $this->whenLoaded('genres', OptionResource::collection($this->genres)),
            'vocal' => $this->whenLoaded('vocal', VocalOptionResource::make($this->vocal)),
            'type' => Song::class,
        ];
    }
}
