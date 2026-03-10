<?php

namespace App\Constants;

class ReportConstants
{
    public const WRONG_AUTHOR = "This track wasn't recorded by the artist shown";

    public const AUDIO_ISSUE = "There's an audio problem";

    public const UNDESIRABLE_CONTENT = 'Undesirable content';

    public const WRONG_AUTHOR_CODE = 1;

    public const AUDIO_ISSUE_CODE = 2;

    public const UNDESIRABLE_CONTENT_CODE = 3;

    public static function resolveConstantByType(int $type)
    {
        if ($type > self::getNumberOfTypes() || $type < 1) {
            return '';
        }

        switch ($type) {
            case self::WRONG_AUTHOR_CODE:
                return self::WRONG_AUTHOR;
            case self::AUDIO_ISSUE_CODE:
                return self::AUDIO_ISSUE;
            case self::UNDESIRABLE_CONTENT_CODE:
                return self::UNDESIRABLE_CONTENT;
        }
    }

    public static function getTypes(): array
    {
        return [
            self::WRONG_AUTHOR,
            self::AUDIO_ISSUE,
            self::UNDESIRABLE_CONTENT,
        ];
    }

    public static function getNumberOfTypes(): int
    {
        return count(self::getTypes());
    }
}
