<?php

namespace App\Enums;

enum RoleEnum: string
{
    use EnumMethods;

    case Listener = 'listener';
    case Creator = 'creator';
    case Admin = 'admin';
    case SuperAdmin = 'super-admin';

    public static function getUploadingRoles(): array
    {
        return [
            self::Creator,
            self::Admin,
            self::SuperAdmin,
        ];
    }

    public static function getAdminRoles(): array
    {
        return [
            self::Admin,
            self::SuperAdmin,
        ];
    }
}
