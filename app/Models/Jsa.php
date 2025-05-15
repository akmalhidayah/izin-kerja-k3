<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jsa extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_id', 'nama_perusahaan', 'no_jsa', 'nama_jsa',
        'departemen', 'area_kerja', 'tanggal', 'dibuat_nama',
        'dibuat_signature', 'disetujui_nama', 'disetujui_signature',
        'diverifikasi_nama', 'diverifikasi_signature', // ✅ tambahkan
        'langkah_kerja'
    ];

    protected $casts = [
        'langkah_kerja' => 'array',
        'tanggal' => 'date',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
}
