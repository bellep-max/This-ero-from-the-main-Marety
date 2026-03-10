<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class NotificationController extends ApiController
{
    public function markAsRead(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== auth()->id()) {
            return $this->forbidden('Unauthorized action.');
        }

        if ($notification->read_at) {
            return $this->success(null, 'Already read');
        }

        $notification->update(['read_at' => now()]);

        return $this->success(null, 'Notification marked as read');
    }
}
