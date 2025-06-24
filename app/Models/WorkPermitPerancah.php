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
'signature_verificator_approval',
'signature_issuer_approval',
'signature_authorizer_approval',
 'perancah_start_date',
    'perancah_start_time',
    'perancah_end_date',
    'perancah_end_time',


                'token',
    ];

    protected $casts = [
        'persyaratan_perancah' => 'array',
        'persyaratan_keselamatan_perancah' => 'array',
    ];

  // Relasi ke notification
    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    // Relasi ke detail melalui notification_id
    public function detail()
    {
        return $this->hasOne(WorkPermitDetail::class, 'notification_id', 'notification_id');
    }

    // Relasi ke closure melalui work_permit_detail_id
    public function closure()
    {
        return $this->hasOneThrough(
            WorkPermitClosure::class,
            WorkPermitDetail::class,
            'notification_id',          // Foreign key on detail
            'work_permit_detail_id',    // Foreign key on closure
            'notification_id',          // Local key on this model
            'id'                        // Local key on detail
        );
    }
}
