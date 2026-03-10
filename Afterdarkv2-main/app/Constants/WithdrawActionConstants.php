<?php

namespace App\Constants;

class WithdrawActionConstants
{
    public const UNPAID = 'unPaid';

    public const MARK_PAID = 'markPaid';

    public const DECLINE = 'decline';

    public static function getAll(): array
    {
        return [
            self::UNPAID,
            self::MARK_PAID,
            self::DECLINE,
        ];
    }
}
