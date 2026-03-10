<?php

namespace App\Constants;

class TypeConstants
{
    public const ARTIST = 'artist';

    public const SONG = 'song';

    public const PLAYLIST = 'playlist';

    public const ALBUM = 'album';

    public const STATION = 'station';

    public const USER = 'user';

    public const PODCAST = 'podcast';

    public const ADVENTURE = 'adventure';

    public const POST = 'post';

    public const EPISODE = 'episode';

    public const QUEUE = 'queue';

    public const RECENT = 'recent';

    public const GENRE = 'genre';

    public const MOOD = 'mood';

    public const COMMUNITY = 'community';

    public const OBSESSED = 'obsessed';

    public static function getChannelsList($named = true): array
    {
        return $named
        ? [
            self::SONG => ucfirst(self::SONG),
            self::ALBUM => ucfirst(self::ALBUM),
            self::ARTIST => ucfirst(self::ARTIST),
            self::STATION => ucfirst(self::STATION),
            self::PLAYLIST => ucfirst(self::PLAYLIST),
            self::PODCAST => ucfirst(self::PODCAST),
            self::USER => ucfirst(self::USER),
            self::ADVENTURE => ucfirst(self::ADVENTURE),
        ] : [
            self::SONG,
            self::ALBUM,
            self::ARTIST,
            self::STATION,
            self::PLAYLIST,
            self::PODCAST,
            self::USER,
            self::ADVENTURE,
        ];
    }
}
