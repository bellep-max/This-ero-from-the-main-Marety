<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Playlist\PlaylistShortResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCollaboratorResource extends JsonResource
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
            'name' => $this->name,
            'username' => $this->username,
            'favorite' => $this->favorite,
            'artwork' => $this->artwork,
            'playlists' => $this->whenLoaded('collaboratedPlaylists', PlaylistShortResource::collection($this->collaboratedPlaylists)),
        ];
    }
}
