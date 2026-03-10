<?php

namespace App\Constants;

class NewsStatusConstants
{
    public const NAME_AWATING_MODERATON = 'Articles waiting for moderation';

    public const CODE_AWATING_MODERATON = 0;

    public const NAME_APPROVED = 'Approved';

    public const CODE_APPROVED = 1;

    public const NAME_ALL = 'All news';

    public const CODE_ALL = 2;

    public static function getFullList(): array
    {
        return [
            self::CODE_AWATING_MODERATON => self::NAME_AWATING_MODERATON,
            self::CODE_APPROVED => self::NAME_APPROVED,
            self::CODE_ALL => self::NAME_ALL,
        ];
    }

    public static function getCodesList(): array
    {
        return [
            self::CODE_AWATING_MODERATON,
            self::CODE_APPROVED,
            self::CODE_ALL,
        ];
    }
}
