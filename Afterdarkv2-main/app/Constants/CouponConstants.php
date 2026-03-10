<?php

namespace App\Constants;

class CouponConstants
{
    public const NAME_PERCENTAGE = 'Percentage';

    public const CODE_PERCENTAGE = 'percentage';

    public const NAME_FIXED = 'Fixed price';

    public const CODE_FIXED = 'fixed';

    public static function getFullList(): array
    {
        return [
            self::CODE_PERCENTAGE => self::NAME_PERCENTAGE,
            self::CODE_FIXED => self::NAME_FIXED,
        ];
    }

    public static function getCodesList(): array
    {
        return [
            self::CODE_PERCENTAGE,
            self::CODE_FIXED,
        ];
    }
}
