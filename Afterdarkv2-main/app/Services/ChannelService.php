<?php

namespace App\Services;

use App\Constants\TypeConstants;
use App\Http\Resources\Playlist\PlaylistShortResource;
use App\Http\Resources\Song\SongShortResource;
use App\Http\Resources\User\UserShortResource;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Channel;
use App\Models\Podcast;
use App\Models\Song;
use App\Models\Station;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class ChannelService
{
    public function getChannelObjects(Channel $channel, bool $asResource = false, int $perPage = 20): LengthAwarePaginator|AnonymousResourceCollection
    {
        switch ($channel->type) {
            case TypeConstants::SONG:
                $objects = $channel->songs()->exists()
                    ? $channel->songs()->paginate($perPage)
                    : Song::query()->visible()->latest()->paginate($perPage);

                if ($asResource) {
                    $objects = SongShortResource::collection($objects);
                }
                break;
            case TypeConstants::ALBUM:
                $objects = $channel->albums()->exists()
                    ? $channel->albums()->paginate($perPage)
                    : Album::query()->latest()->paginate($perPage);
                break;
            case TypeConstants::ARTIST:
                $objects = $channel->artists()->exists()
                    ? $channel->artists()->paginate($perPage)
                    : Artist::query()->latest()->paginate($perPage);
                break;
            case TypeConstants::STATION:
                $objects = $channel->stations()->exists()
                    ? $channel->stations()->paginate($perPage)
                    : Station::query()->latest()->paginate($perPage);
                break;
            case TypeConstants::PLAYLIST:
                $objects = $channel->playlists()->exists()
                    ? $channel->playlists()->paginate($perPage)
                    : Station::query()->latest()->paginate($perPage);
                break;
            case TypeConstants::PODCAST:
                $objects = $channel->podcasts()->exists()
                    ? $channel->podcasts()->paginate($perPage)
                    : Podcast::query()->latest()->paginate($perPage);
                break;
            case TypeConstants::USER:
                $objects = $channel->users()->exists()
                    ? $channel->users()->paginate($perPage)
                    : User::query()->latest()->paginate($perPage);
                break;
        }

        return $objects;
    }

    public function setChannelObjectResource(Channel $channel, array $collection): AnonymousResourceCollection
    {
        return match ($channel->type) {
            TypeConstants::SONG => SongShortResource::collection($collection),
            //            TypeConstants::ALBUM =>
            //            TypeConstants::ARTIST =>
            //            TypeConstants::STATION =>
            TypeConstants::PLAYLIST => PlaylistShortResource::collection($collection),
            //            TypeConstants::PODCAST =>
            TypeConstants::USER => UserShortResource::collection($collection),
        };
    }
}
