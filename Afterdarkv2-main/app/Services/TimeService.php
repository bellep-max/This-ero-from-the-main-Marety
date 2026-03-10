<?php

namespace App\Services;

class TimeService
{
    public static function getHumanReadableTime(int $timestamp): string
    {
        return $timestamp > 3600
            ? date('H:i:s', $timestamp)
            : date('i:s', $timestamp);
    }
}
