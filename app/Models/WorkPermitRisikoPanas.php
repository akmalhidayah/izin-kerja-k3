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
        'pengukuran_gas',
        'persyaratan_kerja_panas',
        'rekomendasi_kerja_aman_tambahan',
        'rekomendasi_kerja_aman_setuju',

        // Bagian 5 - Permohonan
        'requestor_name',
        'signature_requestor',
        'requestor_date',
        'requestor_time',

        // Bagian 6 - Verifikasi
        'verificator_name',
        'signature_verificator',
        'verificator_date',
        'verificator_time',

        // Bagian 7 - Penerbitan
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

        // Bagian 8 - Pengesahan
        'authorizer_name',
        'authorizer_signature',
        'authorizer_date',
        'authorizer_time',

        // Bagian 9 - Pelaksanaan
        'receiver_name',
        'receiver_signature',
        'receiver_date',
        'receiver_time',

        // Bagian 10 - Penutupan
        'requestor_signature_close',
        'issuer_signature_close',
    ];

    protected $casts = [
        'pengukuran_gas' => 'array',
        'persyaratan_kerja_panas' => 'array',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function detail()
    {
        return $this->hasOne(WorkPermitDetail::class, 'notification_id', 'notification_id');
    }
}
