<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPermitListrikTeganganTinggi extends Model
{
    use HasFactory;

    protected $table = 'work_permit_listrik_tegangan_tinggi';

    protected $fillable = [
        'notification_id',
        'permit_number',
        'work_location',
        'work_date',
        'equipment_machine',
        'permit_issuer_name',
        'permit_issuer_phone',
        'job_scope',
        'worker_count',
        'tools_equipment',
        'other_permit_status',
        'other_permit_details',
        'documentation_checks',
        'supporting_document',
        'safety_requirements',
        'energy_isolation_certificate',
        'additional_hazards',
        'issuer_confirmations',
        'handover_issuer_name',
        'handover_issuer_signature',
        'handover_issuer_date',
        'handover_issuer_time',
        'receiver_confirmation',
        'receiver_name',
        'receiver_signature',
        'receiver_date',
        'receiver_time',
        'additional_k3_instructions',
        'token',
        'token_expires_at',
    ];

    protected $casts = [
        'work_date' => 'date',
        'documentation_checks' => 'array',
        'safety_requirements' => 'array',
        'additional_hazards' => 'array',
        'issuer_confirmations' => 'array',
        'handover_issuer_date' => 'date',
        'receiver_date' => 'date',
        'token_expires_at' => 'datetime',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
}
