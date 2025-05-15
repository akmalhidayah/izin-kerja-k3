<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkPermitDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_id', 'permit_type', 'location', 'work_date',
        'job_description', 'equipment', 'worker_count', 'emergency_contact',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function closure()
    {
        return $this->hasOne(WorkPermitClosure::class);
    }
}
