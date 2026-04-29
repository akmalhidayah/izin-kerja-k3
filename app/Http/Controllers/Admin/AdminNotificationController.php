<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function read(Request $request, AdminNotification $notification): RedirectResponse
    {
        abort_unless($notification->recipient_id === $request->user()->id, 403);

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return redirect($notification->url ?: route('admin.dashboard'));
    }

    public function readAll(Request $request): RedirectResponse
    {
        AdminNotification::where('recipient_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back();
    }
}
