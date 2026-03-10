<?php

namespace App\Http\Resources\Podcast;

use App\Http\Resources\OptionResource;
use App\Http\Resources\TagResource;
use App\Http\Resources\User\UserProfileResource;
use App\Models\Podcast;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PodcastEditResource extends JsonResource
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
            'allow_comments' => $this->allow_comments,
            'allow_download' => $this->allow_download,
            'explicit' => $this->explicit,
            'is_visible' => $this->is_visible,
            'language_id' => $this->language_id,
            'country_id' => $this->country_id,
            'categories' => $this->whenLoaded('categories', OptionResource::collection($this->categories)),
            'tags' => $this->whenLoaded('tags', TagResource::collection($this->tags)),
            'user' => $this->whenLoaded('user', UserProfileResource::make($this->user)),
            'type' => Podcast::class,
        ];
    }
}
