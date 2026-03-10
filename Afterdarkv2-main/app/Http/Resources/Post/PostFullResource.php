<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\TagResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostFullResource extends JsonResource
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
            'author' => $this->whenLoaded('user', $this->user->name),
            'short_content' => $this->whenHas('short_content', $this->short_content),
            'full_content' => $this->whenHas('full_content', $this->full_content),
            'tags' => $this->whenLoaded('tags', TagResource::collection($this->tags)),
            'created_at' => $this->created_at->format('F j Y'),
            'artwork' => $this->artwork,
            'allow_comments' => $this->allow_comments,
            'type' => Post::class,
        ];
    }
}
