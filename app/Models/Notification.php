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
        'user_id',             // tambahkan ini agar mass assignment bisa
        'assigned_admin_id',   // kalau mau sekalian
    ];

    // Relasi ke dokumen upload per step (bpjs, ktp, dsb)
    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }

    // Relasi ke Working Permit Umum
    public function umumWorkPermit()
    {
        return $this->hasOne(UmumWorkPermit::class, 'notification_id');
    }
    public function WorkPermitGasPanas()
    {
        return $this->hasOne(\App\Models\WorkPermitGasPanas::class, 'notification_id');
    }
    // Relasi ke Working Permit Air
public function workPermitAir()
{
    return $this->hasOne(\App\Models\WorkPermitAir::class, 'notification_id');
}


    // Relasi ke user yang mengajukan (Vendor/User)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    // Relasi ke seluruh step approval
    public function stepApprovals()
    {
        return $this->hasMany(StepApproval::class);
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
}
