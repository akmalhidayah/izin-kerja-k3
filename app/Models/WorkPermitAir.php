<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPermitAir extends Model
{
    use HasFactory;

    protected $table = 'work_permit_air';

    protected $fillable = [
        'notification_id',
     
        'sketsa_pekerjaan',

        'daftar_pekerja',
        'persyaratan_perairan',

        'rekomendasi_kerja_aman',
        'rekomendasi_kerja_aman_check',

        'permit_requestor_name',
        'signature_permit_requestor',
        'permit_requestor_date',
        'permit_requestor_time',

        'verified_workers',
        'verificator_name',
        'signature_verificator',
        'verificator_date',
        'verificator_time',

        'permit_issuer_name',
        'signature_permit_issuer',
        'senior_manager_name',
        'senior_manager_signature',
        'general_manager_name',
        'general_manager_signature',
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


    ];

    protected $casts = [
        'daftar_pekerja' => 'array',
        'persyaratan_perairan' => 'array',
        'verified_workers' => 'array',
    ];
public function detail()
{
    return $this->hasOne(WorkPermitDetail::class, 'notification_id', 'notification_id');
}

    public function closure()
{
    return $this->hasOneThrough(
        WorkPermitClosure::class,
        WorkPermitDetail::class,
        'notification_id', // Foreign key di WorkPermitDetail
        'work_permit_detail_id', // Foreign key di WorkPermitClosure
        'notification_id', // Local key di WorkPermitAir
        'id' // Local key di WorkPermitDetail
    );
}

}

