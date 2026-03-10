<?php

namespace App\Enums;

enum GroupEnum
{
    use EnumMethods;

    case Administrator;
    case Moderator;
    case Member;
    case Guest;
    case Creator;
}
