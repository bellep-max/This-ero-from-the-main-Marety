<?php

namespace App\Constants;

class RestrictionConstants
{
    public const POST = 'post';

    public const MUSIC = 'music';

    public const COMMENT = 'comment';

    public const PLAYLIST = 'playlist';

    public static function getAll($named = true): array
    {
        return $named
            ? [
                self::POST => ucfirst(self::POST),
                self::MUSIC => ucfirst(self::MUSIC),
                self::COMMENT => ucfirst(self::COMMENT),
                self::PLAYLIST => ucfirst(self::PLAYLIST),
            ] : [
                self::POST,
                self::MUSIC,
                self::COMMENT,
                self::PLAYLIST,
            ];
    }
}
