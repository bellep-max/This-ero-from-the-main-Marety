<?php

namespace App\Enums;

enum SubscriptionStatusEnum: string
{
    use EnumMethods;

    case Active = 'active';
    case Suspended = 'suspended';
    case Cancelled = 'cancelled';
    case Pending = 'approval_pending';
}
