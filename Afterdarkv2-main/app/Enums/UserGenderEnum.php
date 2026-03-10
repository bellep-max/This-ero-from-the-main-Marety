<?php

namespace App\Enums;

enum UserGenderEnum: string
{
    use EnumMethods;

    case Male = 'M';
    case Female = 'F';
    case Other = 'O';
}
