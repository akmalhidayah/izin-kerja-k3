<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPermitPerancah extends Model
{
    use HasFactory;

    protected $table = 'work_permit_perancah';

    protected $fillable = [
        'notification_id',

        // Bagian 2
        'sketsa_perancah',
        'persyaratan_perancah',

        // Bagian 3
        'permit_requestor_name',
        'signature_permit_requestor_perancah',
        'permit_requestor_date',
        'permit_requestor_time',

        // Bagian 4
        'scaffolding_verificator_name',
        'signature_scaffolding_verificator',
        'scaffolding_verificator_date',
        'scaffolding_verificator_time',

        // Bagian 5
        'permit_issuer_name',
        'signature_permit_issuer',
        'permit_issuer_date',
        'permit_issuer_time',

        // Bagian 6
        'izin_berlaku_dari',
        'izin_berlaku_jam_dari',
        'izin_berlaku_sampai',
        'izin_berlaku_jam_sampai',

        // Bagian 7
        'permit_authorizer_name',
        'signature_permit_authorizer',
        'permit_authorizer_date',
        'permit_authorizer_time',

        // Bagian 8
        'permit_receiver_name',
        'signature_permit_receiver',
        'permit_receiver_date',
        'permit_receiver_time',

        // Bagian 9
        'persyaratan_keselamatan_perancah',
        'rekomendasi_keselamatan_perancah',
        'rekomendasi_status',

        // Bagian 10
        'scaffolding_verificator_approval',
        'permit_issuer_approval',
        'permit_authorizer_approval',
    ];

    protected $casts = [
        'persyaratan_perancah' => 'array',
        'persyaratan_keselamatan_perancah' => 'array',
    ];

    // Relasi jika dibutuhkan
    public function detail()
    {
        return $this->belongsTo(WorkPermitDetail::class, 'notification_id', 'notification_id');
    }

    public function closure()
    {
        return $this->belongsTo(WorkPermitClosure::class, 'notification_id', 'notification_id');
    }
}
