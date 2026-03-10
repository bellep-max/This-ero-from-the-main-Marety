<?php

namespace App\Constants;

class NewsSubcategorySortConstants
{
    public const NAME_GLOBAL_SETTINGS = 'Global settings';

    public const CODE_GLOBAL_SETTINGS = 0;

    public const NAME_NO = 'No';

    public const CODE_NO = 1;

    public const NAME_YES = 'Yes';

    public const CODE_YES = 2;

    public static function getFullList(): array
    {
        return [
            self::CODE_GLOBAL_SETTINGS => self::NAME_GLOBAL_SETTINGS,
            self::CODE_NO => self::NAME_NO,
            self::CODE_YES => self::NAME_YES,
        ];
    }

    public static function getCodesList(): array
    {
        return [
            self::CODE_GLOBAL_SETTINGS,
            self::CODE_NO,
            self::CODE_YES,
        ];
    }
}
