<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class ApproveSikController extends Controller
{
public function index()
{
    $permintaansik = Notification::with(['user', 'assignedAdmin', 'sikStep']) // ← ini penting
        ->latest()
        ->get();

    return view('admin.approvesik', compact('permintaansik'));
}

}
