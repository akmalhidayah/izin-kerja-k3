<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPermitPengangkatan extends Model
{
    use HasFactory;

    protected $table = 'work_permit_pengangkatan';

    protected $fillable = [
        'notification_id',

        // Bagian 1
        'jenis_tipe',
        'max_boom',
        'min_boom',
        'kapasitas_maks',
        'swl_main_block',
        'swl_aux_block',
        'max_radius',
        'min_radius',
        'swl_chain',
        'swl_master_link',
        'swl_shackle',
        'swl_hammer_lock',
        'swl_spreader_bar',
        'swl_hook',
        'swl_pulley',
        'swl_anchor',
        'berat_beban',
        'berat_man_basket',

        // Bagian 2
        'teknik_pengikatan',

        // Bagian 3
        'wire_rope_sling',

        // Bagian 4
        'diagram_pesawat',
        'diagram_pengikatan',

        // Bagian 5
        'rigger_name',
        'signature_rigger',
        'operator_name',
        'signature_operator',
        'verificator_name',
        'signature_verificator',
    ];

    protected $casts = [
        'teknik_pengikatan' => 'array',
        'wire_rope_sling' => 'array',
    ];
}
