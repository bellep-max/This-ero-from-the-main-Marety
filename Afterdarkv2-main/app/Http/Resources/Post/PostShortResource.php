<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\TagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PostShortResource extends JsonResource
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
            'preview' => $this->whenHas('short_content', Str::limit(strip_tags($this->short_content), 50)),
            'tags' => $this->whenLoaded('tags', fn() => $this->tags ? TagResource::collection($this->tags) : []),
            'created_at' => $this->created_at->format('F j Y'),
            'artwork' => $this->artwork,
            //            'artwork_thumb' => $this->getFirstMediaUrl('artwork', 'thumbnail'),
            'permalink' => $this->permalink,
        ];
    }
}
