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

    // Relasi ke user yang mengajukan (Vendor/User)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke admin yang ditugaskan menangani permintaan
    public function handledBy()
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    // Relasi ke seluruh step approval
    public function stepApprovals()
    {
        return $this->hasMany(StepApproval::class);
    }
}
