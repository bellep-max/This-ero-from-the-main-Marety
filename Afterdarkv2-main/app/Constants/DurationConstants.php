<?php

namespace App\Constants;

class DurationConstants
{
    public const NONE = 0;

    public const WEEK = 7;

    public const MONTH = 30;

    public const YEAR = 365;

    public const NAME_DAY = 'day';

    public const NAME_WEEK = 'week';

    public const NAME_MONTH = 'month';

    public const NAME_YEAR = 'year';

    public static function getNames($named = true): array
    {
        return $named ? [
            self::NAME_DAY => ucfirst(self::NAME_DAY),
            self::NAME_WEEK => ucfirst(self::NAME_WEEK),
            self::NAME_MONTH => ucfirst(self::NAME_MONTH),
            self::NAME_YEAR => ucfirst(self::NAME_YEAR),
        ] : [
            self::NAME_DAY,
            self::NAME_WEEK,
            self::NAME_MONTH,
            self::NAME_YEAR,
        ];
    }
}
