<?php

namespace App\Enums;

enum TrackTypeEnum: string
{
    use EnumMethods;

    case Song = 'song';
    case Adventure = 'adventure';
    case Podcast = 'podcast';
}
