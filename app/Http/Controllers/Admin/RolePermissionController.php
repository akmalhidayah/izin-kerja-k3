<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
   public function index()
{
    $notifications = Notification::with('assignedAdmin')->paginate(10);
    $admins = User::where('usertype', 'admin')->get();
    return view('admin.role-permission.index', compact('notifications', 'admins'));
}

    public function edit(Notification $notification)
    {
        $admins = User::where('usertype', 'admin')->get();
        return view('admin.role-permission.edit', compact('notification', 'admins'));
    }

    public function update(Request $request, Notification $notification)
    {
        $request->validate([
            'assigned_admin_id' => 'nullable|exists:users,id',
        ]);

        $notification->update([
            'assigned_admin_id' => $request->assigned_admin_id,
        ]);

        return redirect()->route('admin.role_permission.index')->with('success', 'Assigned admin updated.');
    }
    public function destroy(Notification $notification)
{
    $notification->delete();
    return redirect()->route('admin.role_permission.index')->with('success', 'Notification berhasil dihapus.');
}

}