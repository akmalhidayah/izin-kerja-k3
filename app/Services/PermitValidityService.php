<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\PermitArchive;
use App\Models\StepApproval;
use App\Models\User;
use App\Support\PermitRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Aturan masa berlaku Working Permit (umum + khusus) per pengajuan.
 *
 * Vendor : jam mulai saat admin menyetujui step working_permit; kedaluwarsa setelah N hari
 *          selama step SIK belum disetujui. Saat kedaluwarsa, step setelah working_permit terkunci
 *          (hanya di tampilan + dicek ulang di server); status step lain tidak diubah.
 * PGO    : jam mulai saat permit pertama dibuat; kedaluwarsa bila belum disetujui dalam N hari.
 *          Setelah disetujui, selesai dan tidak kedaluwarsa.
 *
 * Pengajuan yang belum punya permit_expires_at (data lama) tidak pernah kedaluwarsa.
 * Status "kedaluwarsa"/"terkunci" hanya label tampilan, tidak disimpan ke step_approvals.
 */
class PermitValidityService
{
    public const UI_EXPIRED = 'kedaluwarsa';
    public const UI_LOCKED = 'terkunci';

    /** Step vendor yang terkunci saat permit kedaluwarsa (urutan setelah working_permit). */
    public const VENDOR_STEPS_AFTER_PERMIT = [
        'fakta_integritas',
        'sertifikasi_ak3',
        'ktp',
        'surat_kesehatan',
        'struktur_organisasi',
        'post_test',
        'bukti_serah_terima',
        'sik',
    ];

    /** Kolom tanda tangan (termasuk paraf) yang dikosongkan saat permit dibuat ulang. */
    private const SIGNATURE_COLUMN = '/(signature|_sign$|^paraf_)/';

    /** Kolom tanggal/jam/periode berlaku yang dikosongkan saat permit dibuat ulang. */
    private const DATE_COLUMN = '/(_date$|_time$|^izin_berlaku_)/';

    /** Kunci di dalam data JSON (mis. daftar pekerja) yang dikosongkan saat permit dibuat ulang. */
    private const NESTED_CLEAR_KEYS = ['signature', 'sign', 'paraf', 'ttd', 'tgl', 'tanggal', 'jam', 'waktu', 'masuk', 'keluar', 'date', 'time'];

    public function __construct(private readonly PermitRegistry $registry)
    {
    }

    public function days(): int
    {
        return max(1, (int) config('permit_validity.days', 7));
    }

    /**
     * @param  array<string, string>|null  $statuses  status StepApproval per step (opsional, hemat query)
     */
    public function state(Notification $notification, ?array $statuses = null): array
    {
        $notification->loadMissing('user');
        $isPgo = $notification->user?->usertype === User::USERTYPE_PGO;
        $statuses ??= $this->statuses($notification->id);

        $expiresAt = $notification->permit_expires_at;
        $finished = $isPgo
            ? ($statuses['working_permit'] ?? null) === 'disetujui'
            : ($statuses['sik'] ?? null) === 'disetujui';
        $expired = $expiresAt !== null && ! $finished && now()->greaterThanOrEqualTo($expiresAt);
        $active = $expiresAt !== null && ! $finished && ! $expired;

        return [
            'is_pgo' => $isPgo,
            'cycle' => (int) ($notification->permit_cycle ?: 1),
            'valid_from' => $notification->permit_valid_from,
            'expires_at' => $expiresAt,
            'active' => $active,
            'expired' => $expired,
            'finished' => $finished,
            'remaining_days' => $active ? (int) ceil(now()->diffInMinutes($expiresAt, true) / 1440) : null,
            'days' => $this->days(),
        ];
    }

    public function isExpired(Notification $notification): bool
    {
        return $this->state($notification)['expired'];
    }

    /**
     * Kunci step setelah working_permit di dashboard vendor.
     *
     * Gerbang tiap step di Blade melihat status step sebelumnya, jadi gerbang working_permit
     * sampai step terakhir diberi label "terkunci" agar step 6 dan seterusnya tertutup.
     */
    public function lockStepsAfterPermit(array $steps, array $gateStatuses): array
    {
        $index = null;

        foreach ($steps as $i => $step) {
            if ($step['code'] === 'working_permit') {
                $index = $i;
                break;
            }
        }

        if ($index === null) {
            return [$steps, $gateStatuses];
        }

        foreach ($steps as $i => $step) {
            if ($i === $index) {
                $steps[$i]['status'] = self::UI_EXPIRED;
            }

            if ($i > $index) {
                $steps[$i]['enabled'] = false;
            }

            if ($i >= $index) {
                $gateStatuses[$step['code']] = self::UI_LOCKED;
            }
        }

        $gateStatuses['sik'] = self::UI_LOCKED;

        return [$steps, $gateStatuses];
    }

    /** Pesan penolakan bila admin mencoba menyetujui step yang terkunci karena permit kedaluwarsa. */
    public function guardStatusChange(Notification $notification, string $step, string $newStatus): ?string
    {
        if ($newStatus !== 'disetujui') {
            return null;
        }

        $state = $this->state($notification);

        if (! $state['expired']) {
            return null;
        }

        if ($step === 'working_permit') {
            return 'Permit sudah kedaluwarsa. Pemohon harus membuat permit baru, lalu permit baru itu yang disetujui.';
        }

        if (! $state['is_pgo'] && in_array($step, self::VENDOR_STEPS_AFTER_PERMIT, true)) {
            return 'Permit kedaluwarsa, sehingga step ini terkunci sampai permit baru disetujui.';
        }

        return null;
    }

    /** Apakah upload untuk step ini harus ditolak karena permit kedaluwarsa. */
    public function locksStep(Notification $notification, ?string $step): bool
    {
        return in_array($step, self::VENDOR_STEPS_AFTER_PERMIT, true)
            && $this->state($notification)['expired'];
    }

    /** Dipanggil setelah admin mengubah status sebuah step. */
    public function onStepStatusChanged(Notification $notification, string $step, ?string $oldStatus, string $newStatus): void
    {
        if ($step !== 'working_permit') {
            return;
        }

        $notification->loadMissing('user');
        $isPgo = $notification->user?->usertype === User::USERTYPE_PGO;
        $wasApproved = $oldStatus === 'disetujui';
        $isApproved = $newStatus === 'disetujui';

        if ($isPgo) {
            if ($isApproved) {
                // PGO selesai saat disetujui: tidak ada masa kedaluwarsa lagi.
                $notification->forceFill(['permit_expires_at' => null])->save();
            } elseif ($wasApproved) {
                // Persetujuan dibatalkan: permit kembali menunggu dan jam berjalan lagi.
                $notification->forceFill([
                    'permit_valid_from' => now(),
                    'permit_expires_at' => now()->addDays($this->days()),
                ])->save();
            }

            return;
        }

        if ($isApproved && ! $wasApproved) {
            $notification->forceFill([
                'permit_valid_from' => now(),
                'permit_expires_at' => now()->addDays($this->days()),
            ])->save();
        } elseif (! $isApproved && $wasApproved && ! $this->state($notification)['expired']) {
            // Persetujuan dibatalkan sebelum kedaluwarsa: jam dihentikan sampai disetujui lagi.
            // Bila sudah kedaluwarsa, jam dibiarkan agar pemohon tetap wajib membuat permit baru.
            $notification->forceFill(['permit_valid_from' => null, 'permit_expires_at' => null])->save();
        }
    }

    /** Dipanggil saat sebuah permit pertama kali dibuat (lihat trait StartsPermitValidityClock). */
    public function onPermitCreated(int $notificationId): void
    {
        $notification = Notification::with('user')->find($notificationId);

        if (! $notification
            || $notification->user?->usertype !== User::USERTYPE_PGO
            || $notification->permit_expires_at !== null) {
            return;
        }

        if (($this->statuses($notificationId)['working_permit'] ?? null) === 'disetujui') {
            return;
        }

        $notification->forceFill([
            'permit_valid_from' => now(),
            'permit_expires_at' => now()->addDays($this->days()),
        ])->save();
    }

    /**
     * Buat permit periode baru: arsipkan data + PDF semua permit, kosongkan tanda tangan dan
     * tanggal, putar token, lalu kembalikan step working_permit ke "menunggu" persetujuan admin.
     *
     * @return int nomor periode baru
     */
    public function renew(Notification $notification, ?User $actor = null): int
    {
        $notification->loadMissing('user');
        $isPgo = $notification->user?->usertype === User::USERTYPE_PGO;
        $cycle = (int) ($notification->permit_cycle ?: 1);
        $permits = $this->registry->existingPermits($notification->id);

        // Bila semua permit sudah dihapus admin, tidak ada yang diarsipkan; kunci tetap dibuka
        // agar pemohon bisa membuat permit dari awal.
        @set_time_limit(180);

        // PDF dibuat dari data lama, sebelum apa pun direset.
        $pdfPaths = [];
        foreach (array_keys($permits) as $type) {
            $pdfPaths[$type] = $this->archivePdf($notification->id, $type, $cycle);
        }

        $validFrom = $notification->permit_valid_from;
        $expiresAt = $notification->permit_expires_at;

        try {
            DB::transaction(function () use ($notification, $permits, $pdfPaths, $cycle, $validFrom, $expiresAt, $actor, $isPgo) {
                foreach ($permits as $type => $permit) {
                    [$detail, $closure] = $this->registry->detailAndClosure($type, $notification->id);

                    PermitArchive::create([
                        'notification_id' => $notification->id,
                        'cycle' => $cycle,
                        'permit_type' => $type,
                        'valid_from' => $validFrom,
                        'expires_at' => $expiresAt,
                        'snapshot' => [
                            'permit' => $permit->attributesToArray(),
                            'detail' => $detail?->attributesToArray(),
                            'closure' => $closure?->attributesToArray(),
                        ],
                        'pdf_path' => $pdfPaths[$type] ?? null,
                        'archived_by' => $actor?->id,
                    ]);

                    $this->clearForNewCycle($permit, ['notification_id', 'token', 'token_expires_at']);
                    $permit->forceFill([
                        'token' => (string) Str::uuid(),
                        'token_expires_at' => now()->addDays(max(1, (int) config('permit_tokens.expires_in_days', 3))),
                    ])->save();

                    if ($detail) {
                        $this->clearForNewCycle($detail, ['notification_id', 'permit_type']);
                    }

                    if ($closure) {
                        $this->clearForNewCycle($closure, ['work_permit_detail_id']);
                    }
                }

                StepApproval::updateOrCreate(
                    ['notification_id' => $notification->id, 'step' => 'working_permit'],
                    ['status' => 'menunggu', 'catatan' => null, 'approved_by' => null]
                );

                $notification->forceFill([
                    'permit_cycle' => $cycle + 1,
                    'permit_valid_from' => $isPgo ? now() : null,
                    'permit_expires_at' => $isPgo ? now()->addDays($this->days()) : null,
                ])->save();
            });
        } catch (\Throwable $e) {
            foreach ($pdfPaths as $path) {
                if ($path) {
                    Storage::disk(PermitArchive::DISK)->delete($path);
                }
            }

            throw $e;
        }

        return $cycle + 1;
    }

    /** @return array<string, string> */
    private function statuses(int $notificationId): array
    {
        return StepApproval::where('notification_id', $notificationId)
            ->orderBy('id')
            ->get(['step', 'status'])
            ->groupBy('step')
            ->map(fn ($rows) => strtolower((string) $rows->first()->status))
            ->all();
    }

    /** Render PDF permit lewat preview controller yang sama, lalu simpan di disk privat. */
    private function archivePdf(int $notificationId, string $type, int $cycle): ?string
    {
        try {
            $response = app($this->registry->get($type)['controller'])->preview($notificationId);
            $content = method_exists($response, 'getContent') ? $response->getContent() : null;

            if (! is_string($content) || ! str_starts_with($content, '%PDF-')) {
                return null;
            }

            $path = sprintf(
                'permit-archives/%d/periode-%d/%s-%s.pdf',
                $notificationId,
                $cycle,
                $type,
                Str::lower(Str::random(8))
            );
            Storage::disk(PermitArchive::DISK)->put($path, $content);

            return $path;
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    /** Kosongkan tanda tangan + tanggal/jam; data lain (nama, catatan, checklist) dipertahankan. */
    private function clearForNewCycle(Model $model, array $keep): void
    {
        $keep = array_merge($keep, ['id', 'created_at', 'updated_at']);

        foreach (array_keys($model->getAttributes()) as $column) {
            if (in_array($column, $keep, true)) {
                continue;
            }

            if (preg_match(self::SIGNATURE_COLUMN, $column) || preg_match(self::DATE_COLUMN, $column)) {
                $model->setAttribute($column, null);
                continue;
            }

            $value = $model->getAttribute($column);

            if (is_array($value)) {
                $model->setAttribute($column, $this->clearNested($value));
                continue;
            }

            // Kolom JSON tanpa cast array di model.
            if (is_string($value) && $value !== '' && in_array($value[0], ['[', '{'], true)) {
                $decoded = json_decode($value, true);

                if (is_array($decoded)) {
                    $model->setAttribute($column, json_encode($this->clearNested($decoded)));
                }
            }
        }

        $model->save();
    }

    private function clearNested(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->clearNested($value);
                continue;
            }

            if (is_string($key) && in_array(strtolower($key), self::NESTED_CLEAR_KEYS, true)) {
                $data[$key] = '';
            }
        }

        return $data;
    }
}
