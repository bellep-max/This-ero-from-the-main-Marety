<?php

namespace App\Constants;

class RoleConstants
{
    public const ADMIN = 1;

    public const MODERATOR = 2;

    public const USER_SUBSCRIPTION = 3;

    public const SITE_SUBSCRIPTION = 4;

    public const MEMBER = 5;

    public const GUEST = 6;

    public const CREATOR = 7;

    public const PODCASTER = 8;

    public static function getMainRoles(): array
    {
        return [
            self::ADMIN,
            self::MEMBER,
            self::GUEST,
        ];
    }
}
