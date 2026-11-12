<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'number',
        'description',
        'file',
        'status',
        'user_id',
        'assigned_admin_id',
    ];

    // === RELASI ===

    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }

    public function stepApprovals()
    {
        return $this->hasMany(StepApproval::class);
    }

    public function stepApprovalLogs()
    {
        return $this->hasMany(StepApprovalLog::class);
    }

    public function jsa()
    {
        return $this->hasOne(Jsa::class, 'notification_id');
    }

    public function kontraktor()
    {
        return $this->hasOne(DataKontraktor::class, 'notification_id');
    }

    public function umumWorkPermit()
    {
        return $this->hasOne(UmumWorkPermit::class, 'notification_id');
    }

    public function workPermitGasPanas()
    {
        return $this->hasOne(WorkPermitGasPanas::class, 'notification_id');
    }

    public function workPermitAir()
    {
        return $this->hasOne(WorkPermitAir::class, 'notification_id');
    }

    public function workPermitBeban()
    {
        return $this->hasOne(WorkPermitBeban::class, 'notification_id');
    }

    public function workPermitKetinggian()
    {
        return $this->hasOne(WorkPermitKetinggian::class, 'notification_id');
    }


    public function workPermitPengangkatan()
    {
        return $this->hasOne(WorkPermitPengangkatan::class, 'notification_id');
    }

    public function workPermitPenggalian()
    {
        return $this->hasOne(WorkPermitPenggalian::class, 'notification_id');
    }

    public function workPermitPerancah()
    {
        return $this->hasOne(WorkPermitPerancah::class, 'notification_id');
    }


    public function workPermitRisikoPanas()
    {
        return $this->hasOne(WorkPermitRisikoPanas::class, 'notification_id');
    }

    public function workPermitRuangTertutup()
    {
        return $this->hasOne(WorkPermitRuangTertutup::class, 'notification_id');
    }

    public function workPermitListrikTeganganTinggi()
    {
        return $this->hasOne(WorkPermitListrikTeganganTinggi::class, 'notification_id');
    }

    public function closure()
    {
        return $this->hasOne(WorkPermitClosure::class, 'notification_id');
    }

    public function detail()
    {
        return $this->hasOne(WorkPermitDetail::class, 'notification_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function sikStep()
    {
        return $this->hasOne(StepApproval::class, 'notification_id')
            ->where('step', 'sik')
            ->where('status', 'disetujui');
    }

    // === SCOPE KONDISI PENGAJUAN (dipakai dashboard admin dan daftar permintaan) ===
    // "Selesai" = step sik disetujui (vendor) atau step working_permit disetujui (PGO).

    public function scopeOfPgo($query)
    {
        return $query->whereHas('user', fn ($u) => $u->where('usertype', 'pgo'));
    }

    public function scopeOfVendor($query)
    {
        return $query->whereHas('user', fn ($u) => $u->where('usertype', '!=', 'pgo'));
    }

    public function scopeSelesai($query)
    {
        return $query->where(function ($q) {
            $q->whereHas('stepApprovals', fn ($a) => $a->where('step', 'sik')->where('status', 'disetujui'))
                ->orWhere(function ($q) {
                    $q->ofPgo()
                        ->whereHas('stepApprovals', fn ($a) => $a->where('step', 'working_permit')->where('status', 'disetujui'));
                });
        });
    }

    public function scopeBelumSelesai($query)
    {
        return $query
            ->whereDoesntHave('stepApprovals', fn ($a) => $a->where('step', 'sik')->where('status', 'disetujui'))
            ->where(function ($q) {
                $q->ofVendor()
                    ->orWhereDoesntHave('stepApprovals', fn ($a) => $a->where('step', 'working_permit')->where('status', 'disetujui'));
            });
    }

    public function scopePerluRevisi($query)
    {
        return $query->whereHas('stepApprovals', fn ($a) => $a->where('status', 'revisi'));
    }

    /** Belum selesai dan tidak sedang revisi: menunggu tindakan admin. */
    public function scopeMenunggu($query)
    {
        return $query->belumSelesai()
            ->whereDoesntHave('stepApprovals', fn ($a) => $a->where('status', 'revisi'));
    }

    public function scopeBelumDitangani($query)
    {
        return $query->belumSelesai()->whereNull('assigned_admin_id');
    }

    /** Menunggu dan tidak ada perubahan approval dalam $days hari terakhir (dibuat lebih dari $days hari lalu). */
    public function scopeMandek($query, int $days = 7)
    {
        $since = now()->subDays($days);

        return $query->menunggu()
            ->where('created_at', '<', $since)
            ->whereDoesntHave('stepApprovals', fn ($a) => $a->where('updated_at', '>=', $since));
    }

    public const KONDISI = ['menunggu', 'belum_ditangani', 'revisi', 'selesai', 'mandek'];

    public function scopeKondisi($query, ?string $kondisi, int $staleDays = 7)
    {
        return match ($kondisi) {
            'menunggu' => $query->menunggu(),
            'belum_ditangani' => $query->belumDitangani(),
            'revisi' => $query->perluRevisi(),
            'selesai' => $query->selesai(),
            'mandek' => $query->mandek($staleDays),
            default => $query,
        };
    }

    protected static function booted()
{
    static::deleting(function ($notification) {
        $notification->uploads->each(fn ($upload) => $upload->delete());
        $notification->stepApprovals->each(fn ($approval) => $approval->delete());
        $notification->stepApprovalLogs()->delete();
        $notification->kontraktor?->delete();

        $notification->umumWorkPermit()?->delete();
        $notification->workPermitAir()?->delete();
        $notification->workPermitGasPanas()?->delete();
        $notification->workPermitBeban()?->delete();
        $notification->workPermitKetinggian()?->delete();
        $notification->workPermitPengangkatan()?->delete();
        $notification->workPermitPenggalian()?->delete();
        $notification->workPermitPerancah()?->delete();
        $notification->workPermitRisikoPanas()?->delete();
        $notification->workPermitRuangTertutup()?->delete();
        $notification->workPermitListrikTeganganTinggi()?->delete();
        $notification->detail?->closure()?->delete();
        $notification->detail()?->delete();
    });
}

}
