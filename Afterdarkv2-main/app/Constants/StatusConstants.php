<?php

namespace App\Constants;

class StatusConstants
{
    public const CANCELLED = 'cancelled';

    public const ACTIVE = 'active';

    public const SUSPENDED = 'suspended';

    public const SUCCESS = 'success';

    public const FAILED = 'failed';

    public static function getAll(): array
    {
        return [
            self::CANCELLED,
            self::ACTIVE,
            self::SUSPENDED,
        ];
    }

    public static function getDefaultConstants(): array
    {
        return [
            0 => self::FAILED,
            1 => self::SUCCESS,
        ];
    }
}
