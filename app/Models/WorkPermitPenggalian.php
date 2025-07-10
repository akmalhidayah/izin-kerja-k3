<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitClosure;

class WorkPermitPenggalian extends Model
{
    use HasFactory;

    protected $table = 'work_permit_penggalian';

    protected $fillable = [
        'notification_id',
        'lokasi_pekerjaan',
        'tanggal_pekerjaan',
        'uraian_pekerjaan',
        'sketsa_penggalian',
        'alat_penggalian',
        'jumlah_pekerja',
        'nomor_darurat',
        'denah',
        'denah_status',
        'file_denah',
        'syarat_penggalian',
        'rekomendasi_tambahan',
        'rekomendasi_status',
        'permit_requestor_name',
        'signature_permit_requestor',
        'permit_requestor_date',
        'permit_requestor_time',
        'verificator_name',
        'signature_verificator',
        'verificator_date',
        'verificator_time',
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
        'token',
    ];

    protected $casts = [
        'denah' => 'array',
        'syarat_penggalian' => 'array',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function detail()
{
    return $this->hasOne(WorkPermitDetail::class, 'notification_id', 'notification_id')
                ->where('permit_type', 'penggalian');
}

public function closure()
{
    return $this->hasOneThrough(
        WorkPermitClosure::class,
        WorkPermitDetail::class,
        'notification_id',        // foreign key di WorkPermitDetail
        'work_permit_detail_id',  // foreign key di WorkPermitClosure
        'notification_id',        // local key di WorkPermitPenggalian
        'id'                      // local key di WorkPermitDetail
    )->where('permit_type', 'penggalian');
}
}
