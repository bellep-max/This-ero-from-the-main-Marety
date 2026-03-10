<?php

namespace App\Enums;

enum AdventureSongTypeEnum: string
{
    use EnumMethods;

    case Heading = 'heading';
    case Root = 'root';
    case Final = 'final';
}
