<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitClosure;

class WorkPermitGasPanas extends Model
{
    use HasFactory;

    protected $table = 'work_permit_gas_panas';

    protected $fillable = [
        'notification_id',

        // Bagian 2
        'daftar_pekerja',
        'sketsa_pekerjaan',

        // Bagian 3
        'checklist_kerja_aman',

        // Bagian 4
        'rekomendasi_tambahan',
        'rekomendasi_status',

        // Bagian 5
        'permit_requestor_name',
        'permit_requestor_sign',
        'permit_requestor_date',
        'permit_requestor_time',

        // Bagian 6
        'verified_workers',
        'verificator_name',
        'verificator_sign',
        'verificator_date',
        'verificator_time',

        // Bagian 7
        'permit_issuer_name',
        'permit_issuer_sign',
        'permit_issuer_date',
        'permit_issuer_time',
        'izin_berlaku_dari',
        'izin_berlaku_jam_dari',
        'izin_berlaku_sampai',
        'izin_berlaku_jam_sampai',

        // Bagian 8
        'permit_authorizer_name',
        'permit_authorizer_sign',
        'permit_authorizer_date',
        'permit_authorizer_time',

        // Bagian 9
        'permit_receiver_name',
        'permit_receiver_sign',
        'permit_receiver_date',
        'permit_receiver_time',

                'token',
    ];

    protected $casts = [
        'daftar_pekerja' => 'array',
        'checklist_kerja_aman' => 'array',
        'verified_workers' => 'array',

        'permit_requestor_date' => 'date',
        'permit_issuer_date' => 'date',
        'permit_authorizer_date' => 'date',
        'permit_receiver_date' => 'date',
        'verificator_date' => 'date',
        'izin_berlaku_dari' => 'date',
        'izin_berlaku_sampai' => 'date',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
public function detail()
{
    return $this->hasOne(\App\Models\WorkPermitDetail::class, 'notification_id', 'notification_id')
                ->where('permit_type', 'gaspanas');
}

public function closure()
{
    return $this->hasOneThrough(
        \App\Models\WorkPermitClosure::class,
        \App\Models\WorkPermitDetail::class,
        'notification_id',      // foreign key on WorkPermitDetail
        'work_permit_detail_id', // foreign key on WorkPermitClosure
        'notification_id',       // local key on WorkPermitGasPanas
        'id'                     // local key on WorkPermitDetail
    )->where('permit_type', 'gaspanas');
}

}
