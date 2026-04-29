<?php

namespace App\Http\Controllers;

use App\Models\Notification;

abstract class Controller
{
    protected function findAccessibleNotification($notificationId): ?Notification
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        $query = Notification::where('id', $notificationId);

        if (in_array($user->usertype, ['user', 'pgo'], true)) {
            $query->where('user_id', $user->id);
        }

        return $query->first();
    }
}
