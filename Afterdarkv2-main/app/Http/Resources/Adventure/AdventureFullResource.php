<?php

namespace App\Http\Resources\Adventure;

use App\Http\Resources\OptionResource;
use App\Http\Resources\TagResource;
use App\Http\Resources\User\UserShortResource;
use App\Models\Adventure;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdventureFullResource extends JsonResource
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
            'path' => $this->path,
            'allow_comments' => $this->whenLoaded('property', $this->property?->allow_comments),
            'is_visible' => $this->whenLoaded('property', $this->property?->is_visible),
            'created_at' => $this->created_at->format('d.m.Y'),
            'children' => self::collection($this->roots()->exists() ? $this->roots : $this->finals),
            'tags' => $this->whenLoaded('tags', TagResource::collection($this->tags)),
            'genres' => $this->whenLoaded('genres', OptionResource::collection($this->genres)),
            'user' => $this->whenLoaded('user', UserShortResource::make($this->user)),
            'likes' => $this->whenHas('loves_count', $this->loves_count),
            'type' => Adventure::class,
            'is_liked' => $this->when(auth()->check(), auth()->user()?->loves()
                ->where('loveable_id', $this->id)
                ->where('loveable_type', Adventure::class)
                ->exists()
            ),
        ];
    }
}
