<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserFollowerResource extends JsonResource
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
            'artwork' => $this->artwork,
            'tracks' => $this->tracks_count,
            'adventures' => $this->adventure_headers_count,
            'playlists' => $this->my_playlists_count,
        ];
    }
}
