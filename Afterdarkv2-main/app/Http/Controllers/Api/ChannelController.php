<?php

/**
 * Created by PhpStorm.
 * User: lechchut
 * Date: 6/3/19
 * Time: 10:52 AM.
 */

namespace App\Http\Controllers\Api;

use App\Constants\TypeConstants;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Channel;
use App\Models\Playlist;
use App\Models\Podcast;
use App\Models\Song;
use App\Models\Station;
use App\Models\User;
use Illuminate\Http\Request;

class ChannelController
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $channel = Channel::query()
            ->where('alt_name', $this->request->route('slug'))
            ->firstOrFail();

        if ($channel->object_type == TypeConstants::ARTIST) {
            $channel->objects = Artist::query()->whereIn('id', explode(',', $channel->object_ids))->paginate(20);
        } elseif ($channel->object_type == TypeConstants::SONG) {
            $channel->objects = Song::query()->whereIn('id', explode(',', $channel->object_ids))->paginate(20);
        } elseif ($channel->object_type == TypeConstants::ALBUM) {
            $channel->objects = Album::query()->whereIn('id', explode(',', $channel->object_ids))->paginate(20);
        } elseif ($channel->object_type == TypeConstants::PLAYLIST) {
            $channel->objects = Playlist::query()->whereIn('id', explode(',', $channel->object_ids))->paginate(20);
        } elseif ($channel->object_type == TypeConstants::STATION) {
            $channel->objects = Station::query()->whereIn('id', explode(',', $channel->object_ids))->paginate(20);
        } elseif ($channel->object_type == TypeConstants::USER) {
            $channel->objects = User::query()->whereIn('id', explode(',', $channel->object_ids))->paginate(20);
        } elseif ($channel->object_type == TypeConstants::PODCAST) {
            $channel->objects = Podcast::query()->whereIn('id', explode(',', $channel->object_ids))->paginate(20);
        }

        return response()->json($channel->objects);
    }
}
