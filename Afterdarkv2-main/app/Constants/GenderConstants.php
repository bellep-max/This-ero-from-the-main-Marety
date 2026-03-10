<?php

namespace App\Constants;

class GenderConstants
{
    public const FEMALE_CODE = 1;

    public const MALE_CODE = 2;

    public const TRANS_MALE_CODE = 3;

    public const TRANS_FEMALE_CODE = 4;

    public const NON_BINARY_CODE = 5;

    public const ANYONE_CODE = 6;

    public const FEMALE_NAME = '[F] female';

    public const MALE_NAME = '[M] male';

    public const TRANS_MALE_NAME = '[TM] trans male';

    public const TRANS_FEMALE_NAME = '[TF] trans female';

    public const NON_BINARY_NAME = '[NB] non-binary';

    public const ANYONE_NAME = '[A] anyone';

    public static function getGenderCodes(): array
    {
        return [
            self::FEMALE_CODE,
            self::MALE_CODE,
            self::TRANS_MALE_CODE,
            self::TRANS_FEMALE_CODE,
            self::NON_BINARY_CODE,
            self::ANYONE_CODE,
        ];
    }

    public static function getGenderNames(): array
    {
        return [
            self::FEMALE_NAME,
            self::MALE_NAME,
            self::TRANS_MALE_NAME,
            self::TRANS_FEMALE_NAME,
            self::NON_BINARY_NAME,
            self::ANYONE_NAME,
        ];
    }
}
