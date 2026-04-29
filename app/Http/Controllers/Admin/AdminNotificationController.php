<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\AdminNotification;

class AdminNotificationController extends Controller
{
    public function read(Request $request, int $notification): RedirectResponse
    {
        $adminNotification = AdminNotification::where('id', $notification)
            ->where('recipient_id', $request->user()->id)
            ->first();

        if (!$adminNotification) {
            return redirect()->route('admin.dashboard');
        }

        if (!$adminNotification->read_at) {
            $adminNotification->update(['read_at' => now()]);
        }

        return redirect($adminNotification->url ?: route('admin.dashboard'));
    }

    public function readAll(Request $request): RedirectResponse
    {
        AdminNotification::where('recipient_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back();
    }
}
