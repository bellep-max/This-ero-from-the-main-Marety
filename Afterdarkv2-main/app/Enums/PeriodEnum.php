<?php

namespace App\Enums;

enum PeriodEnum: string
{
    use EnumMethods;

    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
}
