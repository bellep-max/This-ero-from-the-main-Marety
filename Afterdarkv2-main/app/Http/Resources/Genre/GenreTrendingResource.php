<?php

namespace App\Http\Resources\Genre;

use App\Http\Resources\Song\SongShortResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GenreTrendingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->name,
            'slug' => $this->alt_name,
            'artwork' => $this->artwork,
            'songs' => SongShortResource::collection($this->songs),
        ];
    }
}
