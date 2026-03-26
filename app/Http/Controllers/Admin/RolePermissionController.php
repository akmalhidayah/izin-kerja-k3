<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\StepApproval;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class RolePermissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::with(['user', 'assignedAdmin']);

        // 🔍 SEARCH
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // 🎯 FILTER USERTYPE (🔥 BARU)
        if ($request->filled('usertype')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('usertype', $request->usertype);
            });
        }

        // 📥 GET DATA
        $notifications = $query->latest()->get();

        // 📊 HITUNG PROGRESS (OPTIMIZED)
        $stepApprovals = StepApproval::whereIn('notification_id', $notifications->pluck('id'))
            ->get()
            ->groupBy('notification_id');

        foreach ($notifications as $notif) {
            $steps = $stepApprovals[$notif->id] ?? collect();
            $approved = $steps->where('status', 'disetujui')->count();

            $notif->current_step = $approved;
            $notif->progress = round(($approved / 13) * 100);
            $notif->is_done = $approved >= 13;
            $notif->status = $notif->is_done ? 'selesai' : ($notif->status ?? 'menunggu');
        }

        // 🎯 FILTER STATUS
        if ($request->filled('status_filter')) {
            if ($request->status_filter === 'selesai') {
                $notifications = $notifications->filter(fn ($n) => $n->is_done)->values();
            } elseif ($request->status_filter === 'belum') {
                $notifications = $notifications->filter(fn ($n) => !$n->is_done)->values();
            }
        }

        // 📄 PAGINATION MANUAL
        $perPage = 10;
        $currentPage = $request->input('page', 1);

        $paginated = new LengthAwarePaginator(
            $notifications->slice(($currentPage - 1) * $perPage, $perPage)->values(),
            $notifications->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // 👤 ADMIN LIST
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

        return redirect()
            ->route('admin.role_permission.index')
            ->with('success', 'Assigned admin berhasil diperbarui.');
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();

        return redirect()
            ->route('admin.role_permission.index')
            ->with('success', 'Notification berhasil dihapus.');
    }
}