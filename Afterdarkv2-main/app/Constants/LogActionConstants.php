<?php

namespace App\Constants;

class LogActionConstants
{
    public const NAME_DELETE = 'Delete';

    public const CODE_DELETE = 1;

    public const NAME_MODERATION = 'Send for moderation';

    public const CODE_MODERATION = 2;

    public const NAME_DISABLE_PUBLISH = 'Disable publishing on the blog homepage';

    public const CODE_DISABLE_PUBLISH = 3;

    public const NAME_UNPIN = 'Unpin';

    public const CODE_UNPIN = 4;

    public const NAME_MOVE = 'Move to other category';

    public const CODE_MOVE = 5;

    public static function getList(bool $named = true): array
    {
        return $named
            ? [
                self::CODE_DELETE => self::NAME_DELETE,
                self::CODE_MODERATION => self::NAME_MODERATION,
                self::CODE_DISABLE_PUBLISH => self::NAME_DISABLE_PUBLISH,
                self::CODE_UNPIN => self::NAME_UNPIN,
                self::CODE_MOVE => self::NAME_MOVE,
            ] : [
                self::CODE_DELETE,
                self::CODE_MODERATION,
                self::CODE_DISABLE_PUBLISH,
                self::CODE_UNPIN,
                self::CODE_MOVE,
            ];
    }
}
