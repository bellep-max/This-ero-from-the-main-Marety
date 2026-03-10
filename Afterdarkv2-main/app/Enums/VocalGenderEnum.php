<?php

namespace App\Enums;

enum VocalGenderEnum: string
{
    use EnumMethods;

    case FemaleCode = '[F]';

    case MaleCode = '[M]';

    case TransMaleCode = '[TM]';

    case TransFemaleCode = '[TF]';

    case NonBinaryCode = '[NB]';

    case AnyoneCode = '[A]';

    case FemaleName = 'female';

    case MaleName = 'male';

    case TransMaleName = 'trans male';

    case TransFemaleName = 'trans female';

    case NonBinaryName = 'non-binary';

    case AnyoneName = 'anyone';

    public static function getVoices(): array
    {
        return [
            self::FemaleCode->value => self::FemaleName->value,
            self::MaleCode->value => self::MaleName->value,
            self::TransMaleCode->value => self::TransMaleName->value,
            self::TransFemaleCode->value => self::TransFemaleName->value,
            self::NonBinaryCode->value => self::NonBinaryName->value,
            self::AnyoneCode->value => self::AnyoneName->value,
        ];
    }
}
