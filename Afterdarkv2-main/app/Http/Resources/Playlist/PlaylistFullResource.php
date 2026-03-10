<?php

namespace App\Http\Resources\Playlist;

use App\Http\Resources\Song\SongShortResource;
use App\Http\Resources\User\UserCollaboratorResource;
use App\Http\Resources\User\UserProfileResource;
use App\Http\Resources\User\UserSubscriberResource;
use App\Models\Playlist;
use App\Services\PlaylistService;
use App\Services\TimeService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaylistFullResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $service = new PlaylistService;

        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'collaboration' => $this->collaboration,
            'artwork' => $this->artwork,
            'allow_comments' => $this->allow_comments,
            'favorite' => $this->favorite,
            'draggable' => $this->user_id === auth()->id(),
            'duration' => $this->whenAggregated('songs', 'duration', 'sum', TimeService::getHumanReadableTime($this->songs_sum_duration ?? 0)),
            'songs_count' => $this->whenHas('songs_count', $this->songs_count),
            'subscribers_count' => $this->whenHas('followers_count', $this->followers_count),
            'collaborators_count' => $this->whenHas('approved_collaborators_count', $this->approved_collaborators_count),
            'songs' => $this->whenLoaded('songs', SongShortResource::collection($this->songs)),
            'created_at' => $this->created_at->format('d.m.Y'),
            'subscribers' => $this->whenLoaded('followers', UserSubscriberResource::collection($this->followers)),
            'collaborators' => $this->whenLoaded('approvedCollaborators', UserCollaboratorResource::collection($this->approvedCollaborators)),
            'is_editable' => $this->whenLoaded('approvedCollaborators', $service->isCollaborator($this->resource)),
            'user' => $this->whenLoaded('user', UserProfileResource::make($this->user)),
            'type' => Playlist::class,
        ];
    }
}
