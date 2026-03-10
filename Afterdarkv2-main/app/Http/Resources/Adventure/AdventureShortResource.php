<?php

namespace App\Http\Resources\Adventure;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdventureShortResource extends JsonResource
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
            'path' => $this->path,
            'artwork' => $this->artwork,
            'duration' => $this->minutes,
            'finals' => $this->whenLoaded('children', self::collection($this->children)),
        ];
    }
}
