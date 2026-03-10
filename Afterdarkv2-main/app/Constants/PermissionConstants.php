<?php

namespace App\Constants;

class PermissionConstants
{
    public const NAME_GROUP_SETTINGS = 'Group settings';

    public const CODE_GROUP_SETTINGS = 0;

    public const NAME_READONLY = 'Read Only';

    public const CODE_READONLY = 1;

    public const NAME_READ_COMMENT = 'Read And Comment';

    public const CODE_READ_COMMENT = 2;

    public const NAME_READ_DENIED = 'Reading denied';

    public const CODE_READ_DENIED = 3;

    public static function getList($named = true): array
    {
        return $named
            ? [
                self::CODE_GROUP_SETTINGS => self::NAME_GROUP_SETTINGS,
                self::CODE_READONLY => self::NAME_READONLY,
                self::CODE_READ_COMMENT => self::NAME_READ_COMMENT,
                self::CODE_READ_DENIED => self::NAME_READ_DENIED,
            ] : [
                self::CODE_GROUP_SETTINGS,
                self::CODE_READONLY,
                self::CODE_READ_COMMENT,
                self::CODE_READ_DENIED,
            ];
    }
}
