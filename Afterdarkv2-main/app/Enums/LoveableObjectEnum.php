<?php

namespace App\Enums;

use App\Models\Adventure;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Episode;
use App\Models\Playlist;
use App\Models\Podcast;
use App\Models\Song;
use App\Models\Station;
use App\Models\User;

enum LoveableObjectEnum: string
{
    use EnumMethods;

    case song = Song::class;
    case playlist = Playlist::class;
    case album = Album::class;
    case artist = Artist::class;
    case station = Station::class;
    case podcast = Podcast::class;
    case episode = Episode::class;
    case user = User::class;
    case adventure = Adventure::class;
}
