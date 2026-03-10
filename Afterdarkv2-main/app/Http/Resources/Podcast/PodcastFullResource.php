<?php

namespace App\Http\Resources\Podcast;

use App\Http\Resources\TagResource;
use App\Http\Resources\User\UserProfileResource;
use App\Http\Resources\User\UserSubscriberResource;
use App\Models\Podcast;
use App\Services\TimeService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PodcastFullResource extends JsonResource
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
            'created_at' => $this->created_at->format('d.m.Y'),
            'duration' => $this->whenAggregated('episodes', 'duration', 'sum', TimeService::getHumanReadableTime($this->episodes_sum_duration ?? 0)),
            'subscribers_count' => $this->whenHas('followers_count', $this->followers_count),
            'subscribers' => $this->whenLoaded('followers', UserSubscriberResource::collection($this->followers)),
            'tags' => $this->whenLoaded('tags', TagResource::collection($this->tags)),
            'likes' => $this->whenHas('fans_count', $this->fans_count),
            'user' => $this->whenLoaded('user', UserProfileResource::make($this->user)),
            'type' => Podcast::class,
        ];
    }
}
