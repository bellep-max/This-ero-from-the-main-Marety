<?php

namespace App\Enums;

enum PermissionEnum: string
{
    use EnumMethods;

    case UPLOAD_AUDIO = 'upload-audio';
    case SUBSCRIBE_TO_PREMIUM = 'subscribe-premium';
    case PATRON_USER = 'patron-user';
    case ADMIN_ACCESS = 'admin-access';

    public function can(): string
    {
        return "can:$this->value";
    }
}
