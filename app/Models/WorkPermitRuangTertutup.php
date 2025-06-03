<?php

// app/Models/WorkPermitRuangTertutup.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPermitRuangTertutup extends Model
{
    use HasFactory;

    protected $table = 'work_permit_ruang_tertutup';

    protected $fillable = [
        'lokasi_pekerjaan',
        'tanggal_pekerjaan',
        'uraian_pekerjaan',
        'peralatan_digunakan',
        'jumlah_pekerja',
        'nomor_darurat',
        'isolasi_listrik',
        'isolasi_non_listrik',
        'pengukuran_gas',
        'syarat_ruang_tertutup',
        'rekomendasi_tambahan',
        'rekomendasi_status',
        'permit_requestor_name',
        'signature_permit_requestor',
        'permit_requestor_date',
        'permit_requestor_time',
        'confined_verificator_name',
        'signature_confined_verificator',
        'confined_verificator_date',
        'confined_verificator_time',
        'permit_issuer_name',
        'signature_permit_issuer',
        'permit_issuer_date',
        'permit_issuer_time',
        'izin_berlaku_dari',
        'izin_berlaku_jam_dari',
        'izin_berlaku_sampai',
        'izin_berlaku_jam_sampai',
        'permit_authorizer_name',
        'signature_permit_authorizer',
        'permit_authorizer_date',
        'permit_authorizer_time',
        'permit_receiver_name',
        'signature_permit_receiver',
        'permit_receiver_date',
        'permit_receiver_time',
        'pekerja_masuk_keluar',
        'live_testing_checklist',
        'live_testing_name',
        'live_testing_signature',
        'live_testing_date',
        'live_testing_time',
        'close_lock_tag',
        'close_tools',
        'close_guarding',
        'close_date',
        'close_time',
        'signature_close_requestor',
        'signature_close_issuer',
    ];

    protected $casts = [
        'isolasi_listrik' => 'array',
        'isolasi_non_listrik' => 'array',
        'pengukuran_gas' => 'array',
        'syarat_ruang_tertutup' => 'array',
        'pekerja_masuk_keluar' => 'array',
        'live_testing_checklist' => 'array',
    ];
}

