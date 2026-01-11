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

    protected static function booted()
{
    static::deleting(function ($notification) {
        $notification->uploads->each(fn ($upload) => $upload->delete());
        $notification->stepApprovals->each(fn ($approval) => $approval->delete());
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
        $notification->detail?->closure()?->delete();
        $notification->detail()?->delete();
    });
}

}
