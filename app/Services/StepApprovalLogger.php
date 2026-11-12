<?php

namespace App\Services;

use App\Models\StepApprovalLog;

/**
 * Satu-satunya tempat menulis riwayat persetujuan.
 * Setiap kode yang mengubah StepApproval.status harus memanggil salah satu metode ini.
 */
class StepApprovalLogger
{
    /**
     * Catat perubahan status/catatan oleh admin. Tidak menulis apa pun bila tidak ada yang berubah.
     */
    public function statusChanged(
        int $notificationId,
        string $step,
        ?string $fromStatus,
        string $toStatus,
        ?string $fromCatatan,
        ?string $toCatatan,
        ?int $actorId
    ): ?StepApprovalLog {
        if ($fromStatus === $toStatus && trim((string) $fromCatatan) === trim((string) $toCatatan)) {
            return null;
        }

        return $this->write($notificationId, $step, StepApprovalLog::ACTION_STATUS_CHANGED, $fromStatus, $toStatus, $toCatatan, $actorId);
    }

    /**
     * Catat step yang kembali "menunggu" karena data/file-nya dihapus.
     * Tidak menulis bila step memang masih menunggu.
     */
    public function reset(int $notificationId, string $step, ?string $fromStatus, string $reason, ?int $actorId): ?StepApprovalLog
    {
        if ($fromStatus === null || $fromStatus === 'menunggu') {
            return null;
        }

        return $this->write($notificationId, $step, StepApprovalLog::ACTION_RESET, $fromStatus, 'menunggu', $reason, $actorId);
    }

    private function write(int $notificationId, string $step, string $action, ?string $from, string $to, ?string $catatan, ?int $actorId): StepApprovalLog
    {
        return StepApprovalLog::create([
            'notification_id' => $notificationId,
            'step' => $step,
            'action' => $action,
            'from_status' => $from,
            'to_status' => $to,
            'catatan' => $catatan !== null && trim($catatan) !== '' ? trim($catatan) : null,
            'actor_id' => $actorId,
            'created_at' => now(),
        ]);
    }
}
