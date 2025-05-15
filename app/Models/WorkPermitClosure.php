<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkPermitClosure extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_permit_detail_id',
        'lock_tag_removed',
        'equipment_cleaned',
        'guarding_restored',
        'closed_date',
        'closed_time',
        'requestor_name',
        'requestor_sign',
        'issuer_name',
        'issuer_sign',
    ];

    public function detail()
    {
        return $this->belongsTo(WorkPermitDetail::class, 'work_permit_detail_id');
    }
}
