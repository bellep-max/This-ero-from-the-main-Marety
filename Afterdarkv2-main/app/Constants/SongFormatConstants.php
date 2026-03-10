<?php

namespace App\Constants;

class SongFormatConstants
{
    public const MP3 = 'mp3';

    public const HLS = 'hls';

    public const HD = 'hd';

    public static function getList(): array
    {
        return [
            self::MP3,
            self::HLS,
            self::HD,
        ];
    }
}
