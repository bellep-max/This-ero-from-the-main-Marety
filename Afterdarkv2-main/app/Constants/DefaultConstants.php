<?php

namespace App\Constants;

class DefaultConstants
{
    public const FALSE = 0;

    public const TRUE = 1;

    public static function getCodesList(): array
    {
        return [
            self::FALSE,
            self::TRUE,
        ];
    }
}
