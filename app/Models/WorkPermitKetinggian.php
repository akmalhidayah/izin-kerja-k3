<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPermitKetinggian extends Model
{
    use HasFactory;

    protected $table = 'work_permit_ketinggian';

    protected $fillable = [
        'notification_id',

        // Bagian 2
        'nama_pekerja',
        'paraf_pekerja',

        // Bagian 3
        'kerja_aman_ketinggian',

        // Bagian 4
        'rekomendasi_tambahan',
        'rekomendasi_ada',

        // Bagian 5
        'permit_requestor_name',
        'signature_permit_requestor',
        'permit_requestor_date',
        'permit_requestor_time',

        // Bagian 6
        'authorized_workers',
        'verifikator_name',
        'signature_verifikator',
        'verifikator_date',
        'verifikator_time',

        // Bagian 7
        'permit_issuer_name',
        'signature_permit_issuer',
        'permit_issuer_date',
        'permit_issuer_time',
        'izin_berlaku_dari',
        'izin_berlaku_jam_dari',
        'izin_berlaku_sampai',
        'izin_berlaku_jam_sampai',

        // Bagian 8
        'permit_authorizer_name',
        'signature_permit_authorizer',
        'permit_authorizer_date',
        'permit_authorizer_time',

        // Bagian 9
        'permit_receiver_name',
        'signature_permit_receiver',
        'permit_receiver_date',
        'permit_receiver_time',
    ];

    protected $casts = [
        'nama_pekerja' => 'array',
        'kerja_aman_ketinggian' => 'array',
        'authorized_workers' => 'array',
    ];
        public function detail()
    {
        return $this->belongsTo(WorkPermitDetail::class, 'work_permit_detail_id');
    }
}
