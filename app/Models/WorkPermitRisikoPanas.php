<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPermitRisikoPanas extends Model
{
    use HasFactory;

    protected $table = 'work_permit_risiko_panas';

    protected $fillable = [
        'notification_id',
        'lokasi_pekerjaan',
        'tanggal_pekerjaan',
        'uraian_pekerjaan',
        'peralatan_digunakan',
        'jumlah_pekerja',
        'nomor_darurat',
        'pengukuran_gas',
        'persyaratan_kerja_panas',
        'rekomendasi_kerja_aman_tambahan',
        'rekomendasi_kerja_aman_setuju',
        'requestor_name',
        'signature_requestor',
        'requestor_date',
        'requestor_time',
        'verificator_name',
        'signature_verificator',
        'verificator_date',
        'verificator_time',
        'permit_issuer_name',
        'signature_permit_issuer',
        'senior_manager_name',
        'signature_senior_manager',
        'general_manager_name',
        'signature_general_manager',
        'izin_berlaku_dari',
        'izin_berlaku_jam_dari',
        'izin_berlaku_sampai',
        'izin_berlaku_jam_sampai',
        'authorizer_name',
        'authorizer_signature',
        'authorizer_date',
        'authorizer_time',
        'receiver_name',
        'receiver_signature',
        'receiver_date',
        'receiver_time',
        'lock_tag',
        'sampah_peralatan',
        'machine_guarding',
        'penutupan_tanggal',
        'penutupan_jam',
        'requestor_name_close',
        'requestor_signature_close',
        'issuer_name_close',
        'issuer_signature_close',
    ];

    protected $casts = [
        'pengukuran_gas' => 'array',
        'persyaratan_kerja_panas' => 'array',
    ];
}

