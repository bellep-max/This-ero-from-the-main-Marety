<?php

namespace App\Constants;

class UserActionConstants
{
    public const CHANGE_GROUP = 'change_usergroup';

    public const UPDATE_GROUP = 'save_change_usergroup';

    public const BAN_USER = 'ban_user';

    public const UPDATE_BAN_USER = 'save_ban_user';

    public const DELETE_COMMENT = 'delete_comment';

    public const DELETE_USER = 'delete';

    public static function getMassActions(): array
    {
        return [
            self::CHANGE_GROUP,
            self::UPDATE_GROUP,
            self::BAN_USER,
            self::UPDATE_BAN_USER,
            self::DELETE_COMMENT,
            self::DELETE_USER,
        ];
    }
}
