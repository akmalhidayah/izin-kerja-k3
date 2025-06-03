<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPermitLifeSaving extends Model
{
    use HasFactory;

    protected $table = 'work_permit_life_saving';

    protected $fillable = [
        'lokasi_pekerjaan',
        'tanggal',
        'uraian_pekerjaan',
        'workers_supervisor',
        'job_supervisor',
        'bahaya_pengendalian',
        'sketsa_pekerjaan',
        'daftar_pekerja',
        'daftar_hadir',
    ];

    protected $casts = [
        'bahaya_pengendalian' => 'array',
        'daftar_pekerja' => 'array',
        'daftar_hadir' => 'array',
    ];
}
