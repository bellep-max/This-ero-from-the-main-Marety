<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    public function markAsRead(Notification $notification): RedirectResponse
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($notification->read_at) {
            return redirect()->back(303);
        }

        $notification->update(['read_at' => now()]);

        return redirect()->back(303);
    }
}
