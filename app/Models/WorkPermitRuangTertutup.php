<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPermitRuangTertutup extends Model
{
    use HasFactory;

    protected $table = 'work_permit_ruang_tertutup';

    protected $fillable = [
        'notification_id',

        // Bagian 2
        'isolasi_listrik',
        'isolasi_non_listrik',

        // Bagian 3
        'pengukuran_gas',

        // Bagian 4
        'syarat_ruang_tertutup',

        // Bagian 5
        'rekomendasi_tambahan',
        'rekomendasi_status',

        // Bagian 6
        'permit_requestor_name',
        'signature_permit_requestor',
        'permit_requestor_date',
        'permit_requestor_time',

        // Bagian 7
        'confined_verificator_name',
        'signature_confined_verificator',
        'confined_verificator_date',
        'confined_verificator_time',

        // Bagian 8
        'permit_issuer_name',
        'signature_permit_issuer',
        'permit_issuer_date',
        'permit_issuer_time',
        'izin_berlaku_dari',
        'izin_berlaku_jam_dari',
        'izin_berlaku_sampai',
        'izin_berlaku_jam_sampai',

        // Bagian 9
        'permit_authorizer_name',
        'signature_permit_authorizer',
        'permit_authorizer_date',
        'permit_authorizer_time',

        // Bagian 10
        'permit_receiver_name',
        'signature_permit_receiver',
        'permit_receiver_date',
        'permit_receiver_time',

        // Bagian 11
        'pekerja_masuk_keluar',

        // Bagian 12
        'live_testing_checklist',
        'live_testing_name',
        'live_testing_signature',
        'live_testing_date',
        'live_testing_time',

                'token',
    ];

    protected $casts = [
        'isolasi_listrik' => 'array',
        'isolasi_non_listrik' => 'array',
        'pengukuran_gas' => 'array',
        'syarat_ruang_tertutup' => 'array',
        'pekerja_masuk_keluar' => 'array',
        'live_testing_checklist' => 'array',
    ];

 public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function detail()
    {
        return $this->hasOne(WorkPermitDetail::class, 'notification_id', 'notification_id');
    }

    public function closure()
    {
        return $this->hasOneThrough(
            WorkPermitClosure::class,
            WorkPermitDetail::class,
            'notification_id',        // Foreign key on WorkPermitDetail
            'work_permit_detail_id',  // Foreign key on WorkPermitClosure
            'notification_id',        // Local key on current model
            'id'                      // Local key on WorkPermitDetail
        );
    }
}
