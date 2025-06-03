<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPermitPerancah extends Model
{
    use HasFactory;

    protected $table = 'work_permit_perancah';

    protected $fillable = [
        'lokasi_pekerjaan',
        'tanggal_pekerjaan',
        'uraian_pekerjaan',
        'sketsa_perancah',
        'peralatan_digunakan',
        'jumlah_pekerja',
        'nomor_darurat',
        'persyaratan_perancah',
        'permit_requestor_name',
        'signature_permit_requestor_perancah',
        'permit_requestor_date',
        'permit_requestor_time',
        'scaffolding_verificator_name',
        'signature_scaffolding_verificator',
        'scaffolding_verificator_date',
        'scaffolding_verificator_time',
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
        'persyaratan_keselamatan_perancah',
        'rekomendasi_keselamatan_perancah',
        'rekomendasi_status',
        'scaffolding_verificator_approval',
        'permit_issuer_approval',
        'permit_authorizer_approval',
        'perancah_start_date',
        'perancah_start_time',
        'perancah_end_date',
        'perancah_end_time',
        'close_lock_tag',
        'close_sampah_peralatan',
        'close_machine_guarding',
        'close_date',
        'close_time',
        'signature_close_requestor',
        'signature_close_issuer',
    ];

    protected $casts = [
        'persyaratan_perancah' => 'array',
        'persyaratan_keselamatan_perancah' => 'array',
    ];
}
