<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPermitProsedurKhusus extends Model
{
    use HasFactory;

    protected $table = 'work_permit_prosedur_khusus';

    protected $fillable = [
        'swp_nama',
        'swp_lokasi',
        'swp_dibuat_tanggal',
        'swp_revisi_terakhir',
        'bahaya',
        'apd',
        'kompetensi',
        'prosedur',
        'referensi',
        'dibuat_oleh',
        'ditinjau_oleh',
        'disetujui_oleh',
        'signature_dibuat_oleh',
        'signature_ditinjau_oleh',
        'signature_disetujui_oleh',
    ];

    protected $casts = [
        'bahaya' => 'array',
        'apd' => 'array',
        'kompetensi' => 'array',
        'prosedur' => 'array',
        'referensi' => 'array',
    ];
}
