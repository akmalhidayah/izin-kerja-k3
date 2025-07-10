<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkPermitBeban extends Model
{
    use HasFactory;

    protected $table = 'work_permit_beban';

    protected $fillable = [
        'notification_id',

        // Bagian 2: Dokumentasi Persyaratan Pengangkatan Beban
        'dok_operator',
        'dok_rigger',
        'dok_sertifikat',
        'dok_loadchart',
        'dok_rencana_pengangkatan',
        'dok_jsa',

        // Bagian 3: Persyaratan Kerja Aman
        'persyaratan_kerja_aman',

        // Bagian 4: Rekomendasi Tambahan
        'rekomendasi_kerja_aman',
        'rekomendasi_status',

        // Bagian 5: Permohonan Izin Kerja
        'permit_requestor_name',
        'signature_permit_requestor',
        'permit_requestor_date',
        'permit_requestor_time',

        // Bagian 6: Verifikasi Izin Kerja
        'verificator_name',
        'signature_verificator',
        'verificator_date',
        'verificator_time',

        // Bagian 7: Penerbitan Izin Kerja
        'permit_issuer_name',
        'signature_permit_issuer',
        'permit_issuer_date',
        'permit_issuer_time',
        'izin_berlaku_dari',
        'izin_berlaku_jam_dari',
        'izin_berlaku_sampai',
        'izin_berlaku_jam_sampai',

        // Bagian 8: Pengesahan Izin Kerja
        'permit_authorizer_name',
        'signature_permit_authorizer',
        'permit_authorizer_date',
        'permit_authorizer_time',

        // Bagian 9: Pelaksanaan Pekerjaan
        'permit_receiver_name',
        'signature_permit_receiver',
        'permit_receiver_date',
        'permit_receiver_time',

         'token',
    ];

    protected $casts = [
        'persyaratan_kerja_aman' => 'array',
        'permit_requestor_date' => 'date',
        'verificator_date' => 'date',
        'permit_issuer_date' => 'date',
        'izin_berlaku_dari' => 'date',
        'izin_berlaku_sampai' => 'date',
        'permit_authorizer_date' => 'date',
        'permit_receiver_date' => 'date',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function detail()
    {
        return $this->hasOne(\App\Models\WorkPermitDetail::class, 'notification_id', 'notification_id')
                    ->where('permit_type', 'beban');
    }

    public function closure()
    {
        return $this->hasOneThrough(
            \App\Models\WorkPermitClosure::class,
            \App\Models\WorkPermitDetail::class,
            'notification_id',
            'work_permit_detail_id',
            'notification_id',
            'id'
        )->where('permit_type', 'beban');
    }
}
