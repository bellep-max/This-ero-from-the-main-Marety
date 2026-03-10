<?php

namespace App\Constants;

class SubscriptionPlanConstants
{
    public const PRICING_CURRENCY = 'USD';
    public const GRACE_DAYS = 7;

    public const PRICE_MONTHLY_VISITOR = 0.00;
    public const PRICE_MONTHLY_LISTENER = 0.00;
    public const PRICE_MONTHLY_CREATOR = 0.00;
    public const PRICE_MONTHLY_LISTENER_PRO = 9.99;
    public const PRICE_MONTHLY_CREATOR_PREMIUM = 24.99;
    public const PRICE_MONTHLY_CREATOR_PRO = 59.99;
    public const PRICE_KNOCK_DOWN = 0.01;
    public const ANNUAL_MONTHS = 10;

    public static function resolveAnnualPrice(float $type): float
    {
        $basis_price = 0;

        if (self::getNumberOfTypes() < 1 || $type < 0) {
            return (float) $basis_price;
        }

        switch ($type) {
            case self::PRICE_MONTHLY_LISTENER:
                $basis_price = self::PRICE_MONTHLY_LISTENER;

                break;
            case self::PRICE_MONTHLY_CREATOR:
                $basis_price = self::PRICE_MONTHLY_CREATOR;

                break;
            case self::PRICE_MONTHLY_LISTENER_PRO:
                $basis_price = self::PRICE_MONTHLY_LISTENER_PRO;

                break;
            case self::PRICE_MONTHLY_CREATOR_PREMIUM:
                $basis_price = self::PRICE_MONTHLY_CREATOR_PREMIUM;

                break;
            case self::PRICE_MONTHLY_CREATOR_PRO:
                $basis_price = self::PRICE_MONTHLY_CREATOR_PRO;

                break;
        }

        if ($basis_price == 0) {
            return $basis_price;
        }

        $basis_price = ceil($basis_price);

        $price = ($basis_price * self::ANNUAL_MONTHS) - self::PRICE_KNOCK_DOWN;

        return $price;
    }

    public static function getTypes(): array
    {
        return [
            self::PRICE_MONTHLY_LISTENER,
            self::PRICE_MONTHLY_CREATOR,
            self::PRICE_MONTHLY_LISTENER_PRO,
            self::PRICE_MONTHLY_CREATOR_PREMIUM,
            self::PRICE_MONTHLY_CREATOR_PRO,
        ];
    }

    public static function getNumberOfTypes(): int
    {
        return count(self::getTypes());
    }
}
