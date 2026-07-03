<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class UserNotificationController extends Controller
{
    public function store(Request $request)
    {
        // Validasi awal
        $validated = $request->validate([
            'type' => 'required|in:po,spk,notif',
            'number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        // Validasi tambahan
        if (in_array($validated['type'], ['po', 'notif']) && empty($validated['number'])) {
            return back()->withErrors([
                'number' => 'Nomor wajib diisi untuk jenis ' . strtoupper($validated['type'])
            ])->withInput();
        }

        // Nomor otomatis untuk SPK
        if ($validated['type'] === 'spk') {
            $validated['number'] = 'SPK-' . now()->format('YmdHis');
        }

        // Upload file jika ada
        if ($request->hasFile('file')) {
            $validated['file'] = $request->file('file')->store('notifications', 'public');
        }

        // ⛔ HAPUS LOGIC UPDATE —> Langsung buat baru saja
        $notification = Notification::create([
            'type' => $validated['type'],
            'number' => $validated['number'] ?? null,
            'description' => $validated['description'] ?? null,
            'file' => $validated['file'] ?? null,
            'user_id' => auth()->id(),
            'status' => 'menunggu',
        ]);

        $this->notifyAdmins($notification);

        return back()->with('success', 'Notifikasi berhasil disimpan!');
    }

    private function notifyAdmins(Notification $notification): void
    {
        $submitter = auth()->user();
        $documentType = strtoupper($notification->type ?? '-');
        $documentNumber = $notification->number ?: '-';
        $jobName = $notification->description ?: 'Tanpa nama pekerjaan';

        User::where('usertype', User::USERTYPE_ADMIN)
            ->pluck('id')
            ->each(function ($adminId) use ($notification, $submitter, $documentType, $documentNumber, $jobName) {
                AdminNotification::create([
                    'recipient_id' => $adminId,
                    'notification_id' => $notification->id,
                    'title' => 'Pengajuan SIK baru',
                    'body' => sprintf(
                        '%s membuat pengajuan %s %s: %s',
                        $submitter?->name ?? 'User',
                        $documentType,
                        $documentNumber,
                        $jobName
                    ),
                    'url' => route('admin.permintaansik.show', $notification->id),
                ]);
            });
    }
}
