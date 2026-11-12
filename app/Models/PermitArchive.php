<?php

namespace App\Models;

use App\Support\PermitRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Salinan satu permit dari periode yang sudah kedaluwarsa (data + PDF). Hanya ditampilkan ke admin.
 * PDF disimpan di disk "local" (privat), bukan "public", karena berisi data personel K3.
 */
class PermitArchive extends Model
{
    public const DISK = 'local';

    protected $fillable = [
        'notification_id',
        'cycle',
        'permit_type',
        'valid_from',
        'expires_at',
        'snapshot',
        'pdf_path',
        'archived_by',
    ];

    protected $casts = [
        'cycle' => 'integer',
        'snapshot' => 'array',
        'valid_from' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleting(function (PermitArchive $archive) {
            if ($archive->pdf_path) {
                Storage::disk(self::DISK)->delete($archive->pdf_path);
            }
        });
    }

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    public function getLabelAttribute(): string
    {
        return PermitRegistry::label($this->permit_type);
    }
}
