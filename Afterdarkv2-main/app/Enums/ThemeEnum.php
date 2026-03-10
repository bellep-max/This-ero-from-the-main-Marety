<?php

namespace App\Enums;

enum ThemeEnum: string
{
    use EnumMethods;

    case Light = 'light';
    case Dark = 'dark';
}
