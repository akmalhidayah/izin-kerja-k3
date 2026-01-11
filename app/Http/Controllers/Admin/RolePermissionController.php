<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\StepApproval;
use App\Models\User;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::with(['user', 'assignedAdmin']);

        // Filter pencarian umum
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        // Ambil semua data untuk filter manual nanti
        $notifications = $query->latest()->get();

        // Hitung step dan progress manual
        foreach ($notifications as $notif) {
            $steps = StepApproval::where('notification_id', $notif->id)->get();
            $approved = $steps->where('status', 'disetujui')->count();

            $notif->current_step = $approved;
            $notif->progress = round(($approved / 13) * 100);
            $notif->is_done = $approved >= 13;
            $notif->status = $notif->is_done ? 'selesai' : ($notif->status ?? 'menunggu');
        }

        // Filter berdasarkan status selesai/belum
        $statusFilter = $request->input('status_filter');
        if ($statusFilter === 'selesai') {
            $notifications = $notifications->filter(fn ($n) => $n->is_done)->values();
        } elseif ($statusFilter === 'belum') {
            $notifications = $notifications->filter(fn ($n) => !$n->is_done)->values();
        }

        // Manual pagination (karena kita filter di collection)
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $currentItems = $notifications->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $notifications->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $admins = User::where('usertype', 'admin')->get();

        return view('admin.role-permission.index', [
            'notifications' => $paginated,
            'admins' => $admins,
        ]);
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
