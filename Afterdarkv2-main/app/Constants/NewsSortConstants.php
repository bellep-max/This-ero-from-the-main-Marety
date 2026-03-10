<?php

namespace App\Constants;

class NewsSortConstants
{
    public const NAME_GLOBAL_SETTINGS = 'Global settings';

    public const CODE_GLOBAL_SETTINGS = 0;

    public const NAME_PUBLICATION_DATE = 'By publication date';

    public const CODE_PUBLICATION_DATE = 1;

    public const NAME_VIEWS = 'By views';

    public const CODE_VIEWS = 2;

    public const NAME_ALPHABETICAL = 'Alphabetical';

    public const CODE_ALPHABETICAL = 3;

    public const NAME_COMMENTS_NUMBER = 'By number of comments';

    public const CODE_COMMENTS_NUMBER = 4;

    public static function getFullList(): array
    {
        return [
            self::CODE_GLOBAL_SETTINGS => self::NAME_GLOBAL_SETTINGS,
            self::CODE_PUBLICATION_DATE => self::NAME_PUBLICATION_DATE,
            self::CODE_VIEWS => self::NAME_VIEWS,
            self::CODE_ALPHABETICAL => self::NAME_ALPHABETICAL,
            self::CODE_COMMENTS_NUMBER => self::NAME_COMMENTS_NUMBER,
        ];
    }

    public static function getCodesList(): array
    {
        return [
            self::CODE_GLOBAL_SETTINGS,
            self::CODE_PUBLICATION_DATE,
            self::CODE_VIEWS,
            self::CODE_ALPHABETICAL,
            self::CODE_COMMENTS_NUMBER,
        ];
    }
}
