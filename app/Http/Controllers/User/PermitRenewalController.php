<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Notification;
use App\Models\User;
use App\Services\PermitValidityService;
use Illuminate\Http\RedirectResponse;

/**
 * Pemohon (vendor/PGO pemilik pengajuan) membuat permit periode baru setelah permit kedaluwarsa.
 */
class PermitRenewalController extends Controller
{
    public function __construct(private readonly PermitValidityService $validity)
    {
    }

    public function store(int $notification): RedirectResponse
    {
        $user = auth()->user();
        $record = Notification::with('user')
            ->where('id', $notification)
            ->where('user_id', $user->id)
            ->first();

        abort_unless($record, 403, 'Akses ditolak.');

        if (! $this->validity->state($record)['expired']) {
            return back()->with('error', 'Permit belum kedaluwarsa, jadi belum perlu dibuat ulang.');
        }

        try {
            $cycle = $this->validity->renew($record, $user);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Permit baru gagal dibuat. Silakan coba lagi atau hubungi admin K3.');
        }

        $this->notifyAdmins($record, $cycle);

        return back()->with('success', 'Permit periode baru sudah dibuat dari data sebelumnya. Lengkapi tanda tangan dan tanggal, lalu tunggu persetujuan admin.');
    }

    private function notifyAdmins(Notification $notification, int $cycle): void
    {
        $recipients = $notification->assigned_admin_id
            ? collect([$notification->assigned_admin_id])
            : User::where('usertype', User::USERTYPE_ADMIN)->pluck('id');

        foreach ($recipients as $adminId) {
            AdminNotification::create([
                'recipient_id' => $adminId,
                'notification_id' => $notification->id,
                'title' => 'Permit diperbarui',
                'body' => sprintf(
                    '%s membuat Working Permit periode %d untuk pengajuan %s %s. Perlu persetujuan ulang.',
                    $notification->user?->name ?? 'Pemohon',
                    $cycle,
                    strtoupper($notification->type ?? '-'),
                    $notification->number ?: '-'
                ),
                'url' => route('admin.permintaansik.show', $notification->id),
            ]);
        }
    }
}
