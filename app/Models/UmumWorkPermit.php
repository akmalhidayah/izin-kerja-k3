<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UmumWorkPermit extends Model
{
    use HasFactory;

    protected $table = 'umum_work_permits';

    protected $fillable = [
        'notification_id',

        'izin_khusus',

        'isolasi_listrik',
        'isolasi_non_listrik',

        'checklist_kerja_aman',

        'rekomendasi_tambahan',
        'rekomendasi_status',

        'permit_requestor_name',
        'permit_requestor_sign',
        'permit_requestor_date',
        'permit_requestor_time',

        'permit_issuer_name',
        'permit_issuer_sign',
        'permit_issuer_date',
        'permit_issuer_time',
        'izin_berlaku_dari',
        'izin_berlaku_jam_dari',
        'izin_berlaku_sampai',
        'izin_berlaku_jam_sampai',

        'permit_authorizer_name',
        'permit_authorizer_sign',
        'permit_authorizer_date',
        'permit_authorizer_time',

        'permit_receiver_name',
        'permit_receiver_sign',
        'permit_receiver_date',
        'permit_receiver_time',

        'live_testing_items',
        'live_testing_name',
        'live_testing_sign',
        'live_testing_date',
        'live_testing_time',

        'token',
    ];

    protected $casts = [
        'izin_khusus' => 'array',
        'isolasi_listrik' => 'array',
        'isolasi_non_listrik' => 'array',
        'checklist_kerja_aman' => 'array',
        'live_testing_items' => 'array',
        'permit_requestor_date' => 'date',
        'permit_issuer_date' => 'date',
        'permit_authorizer_date' => 'date',
        'permit_receiver_date' => 'date',
        'live_testing_date' => 'date',
        'izin_berlaku_dari' => 'date',
        'izin_berlaku_sampai' => 'date',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
// Relasi ke detail
public function detail()
{
    return $this->hasOne(\App\Models\WorkPermitDetail::class, 'notification_id', 'notification_id')
        ->where('permit_type', 'umum');
}

// Relasi ke closure melalui detail
public function closure()
{
    return $this->hasOneThrough(
        \App\Models\WorkPermitClosure::class,
        \App\Models\WorkPermitDetail::class,
        'notification_id',           // Foreign key on WorkPermitDetail
        'work_permit_detail_id',     // Foreign key on WorkPermitClosure
        'notification_id',           // Local key on UmumWorkPermit
        'id'                         // Local key on WorkPermitDetail
    )->where('permit_type', 'umum');
}

}
