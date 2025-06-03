<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataKontraktor extends Model
{
    use HasFactory;

    protected $table = 'data_kontraktors';

    protected $fillable = [
        'notification_id',
        'nama_perusahaan',
        'jenis_pekerjaan',
        'lokasi_kerja',
        'tanggal_mulai',
        'tanggal_selesai',
        'tenaga_kerja',
        'peralatan_kerja',
        'apd',
        'manager_nama',
        'ttd_manager',
        'perusahaan_nama',
        'ttd_perusahaan',
        'diverifikasi_nama',
        'diverifikasi_signature',
        'token',
    ];

    // Peralatan kerja & APD otomatis di-cast ke array
    protected $casts = [
        'tenaga_kerja' => 'array',
        'peralatan_kerja' => 'array',
        'apd' => 'array',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
}
