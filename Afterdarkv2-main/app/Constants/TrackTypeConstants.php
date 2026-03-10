<?php

namespace App\Constants;

class TrackTypeConstants
{
    public const ADVENTURE = 1;

    public const SONG = 2;

    public const PODCAST = 3;

    public static function getTypes(): array
    {
        return [
            self::ADVENTURE,
            self::SONG,
            self::PODCAST,
        ];
    }
}
