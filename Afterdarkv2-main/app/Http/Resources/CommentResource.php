<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'user' => $this->user->name,
            'artwork' => $this->user->artwork,
            'content' => $this->content,
            'is_reply' => !is_null($this->parent_id),
            'created_at' => $this->created_at->diffForHumans(),
            'replies' => $this->whenLoaded('replies', self::collection($this->replies)),
        ];
    }
}
