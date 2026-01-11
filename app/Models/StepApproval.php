<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StepApproval extends Model
{
    protected $fillable = [
        'notification_id', 'step', 'status', 'catatan', 'file_path', 'approved_by',
        'signature_senior_manager', 'signature_manager'
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

public function approvedBy()
{
    return $this->belongsTo(User::class, 'approved_by');
}

}
