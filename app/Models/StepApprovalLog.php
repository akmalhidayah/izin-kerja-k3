<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Jejak perubahan status satu step (siapa, kapan, dari status apa ke apa).
 * Baris hanya ditambah, tidak pernah diubah. Tulis lewat App\Services\StepApprovalLogger.
 */
class StepApprovalLog extends Model
{
    public const ACTION_STATUS_CHANGED = 'status_changed';
    public const ACTION_RESET = 'reset';
    public const ACTION_BACKFILL = 'backfill';

    public const UPDATED_AT = null;

    protected $fillable = [
        'notification_id', 'step', 'action', 'from_status', 'to_status', 'catatan', 'actor_id', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public static function statusLabel(?string $status): string
    {
        return match ($status) {
            null, '' => '-',
            'menunggu' => 'Menunggu',
            'perlu_disetujui' => 'Perlu Disetujui',
            'disetujui' => 'Disetujui',
            'revisi' => 'Perlu Revisi',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            self::ACTION_STATUS_CHANGED => 'Status diubah',
            self::ACTION_RESET => 'Direset karena data dihapus',
            self::ACTION_BACKFILL => 'Data awal (sebelum riwayat aktif)',
            default => ucfirst(str_replace('_', ' ', (string) $this->action)),
        };
    }

    public function getActorNameAttribute(): string
    {
        return $this->actor?->name ?? 'Sistem';
    }

    /** Bentuk ringkas untuk modal riwayat di Blade/Alpine. */
    public function toHistoryRow(array $stepTitles = []): array
    {
        return [
            'waktu' => $this->created_at?->format('d-m-Y H:i') ?? '-',
            'step' => $this->step,
            'step_title' => $stepTitles[$this->step] ?? $this->step,
            'aktor' => $this->actor_name,
            'aksi' => $this->action_label,
            'dari' => self::statusLabel($this->from_status),
            'ke' => self::statusLabel($this->to_status),
            'ke_status' => $this->to_status,
            'catatan' => $this->catatan,
        ];
    }
}
