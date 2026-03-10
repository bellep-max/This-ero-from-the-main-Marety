<?php

namespace App\Http\Resources\Playlist;

use App\Http\Resources\CommentResource;
use App\Http\Resources\Song\SongShortResource;
use App\Http\Resources\User\UserProfileResource;
use App\Services\TimeService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaylistEditResource extends JsonResource
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
            'collaboration' => $this->collaboration,
            'artwork' => $this->artwork,
            'allow_comments' => $this->allow_comments,
            'is_visible' => $this->is_visible,
            'duration' => $this->whenAggregated('songs', 'duration', 'sum', TimeService::getHumanReadableTime($this->songs_sum_duration ?? 0)),
            'songs' => $this->whenLoaded('songs', SongShortResource::collection($this->songs)),
            'genres' => $this->whenLoaded('genres', $this->genres->pluck('id')),
            'created_at' => $this->created_at->format('d.m.Y'),
            'comments' => $this->when($this->allow_comments && $this->whenExistsLoaded('comments'), CommentResource::collection($this->comments)),
            'user' => $this->whenLoaded('user', UserProfileResource::make($this->user)),
        ];
    }
}
