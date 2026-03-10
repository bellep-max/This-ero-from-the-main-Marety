<?php

namespace App\Http\Resources;

use App\Http\Resources\Adventure\AdventureFullResource;
use App\Http\Resources\Playlist\PlaylistShortResource;
use App\Http\Resources\Podcast\PodcastEpisodeResource;
use App\Http\Resources\Podcast\PodcastShortResource;
use App\Http\Resources\Song\SongShortResource;
use App\Http\Resources\User\UserShortResource;
use App\Models\Adventure;
use App\Models\Episode;
use App\Models\Playlist;
use App\Models\Podcast;
use App\Models\Song;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    public function toArray(Request $request): JsonResource
    {
        return $this->getResourceType();
    }

    private function getResourceType(): JsonResource
    {
        return match ($this->loveable_type) {
            Song::class => SongShortResource::make($this->loveable),
            Podcast::class => PodcastShortResource::make($this->loveable),
            Episode::class => PodcastEpisodeResource::make($this->loveable),
            Adventure::class => AdventureFullResource::make($this->loveable),
            Playlist::class => PlaylistShortResource::make($this->loveable),
            User::class => UserShortResource::make($this->loveable),
        };
    }
}
