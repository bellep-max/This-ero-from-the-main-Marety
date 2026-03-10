<?php

namespace App\Constants;

class SearchConstants
{
    public const NAME_EVERYWHERE = 'Everywhere';

    public const CODE_EVERYWHERE = 0;

    public const NAME_TITLE = 'Title';

    public const CODE_TITLE = 1;

    public const NAME_DESCRIPTION = 'Description';

    public const CODE_DESCRIPTION = 2;

    public const NAME_SHORT_CONTENT = 'Short content';

    public const CODE_SHORT_CONTENT = 2;

    public const NAME_FULL_CONTENT = 'Full content';

    public const CODE_FULL_CONTENT = 3;

    public static function getFullList(string $type): array
    {
        return $type === TypeConstants::STATION
            ? [
                self::CODE_EVERYWHERE => self::NAME_EVERYWHERE,
                self::CODE_TITLE => self::NAME_TITLE,
                self::CODE_DESCRIPTION => self::NAME_DESCRIPTION,
            ] : [
                self::CODE_EVERYWHERE => self::NAME_EVERYWHERE,
                self::CODE_TITLE => self::NAME_TITLE,
                self::CODE_SHORT_CONTENT => self::NAME_SHORT_CONTENT,
                self::CODE_FULL_CONTENT => self::NAME_FULL_CONTENT,
            ];
    }

    public static function getCodesList(string $type): array
    {
        return $type === TypeConstants::STATION
            ? [
                self::CODE_EVERYWHERE,
                self::CODE_TITLE,
                self::CODE_DESCRIPTION,
            ] : [
                self::CODE_EVERYWHERE,
                self::CODE_TITLE,
                self::CODE_SHORT_CONTENT,
                self::CODE_FULL_CONTENT,
            ];
    }
}
