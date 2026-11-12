<?php

namespace App\Models\Concerns;

use App\Services\PermitValidityService;

/**
 * Untuk PGO: saat permit pertama kali dibuat, jam masa berlaku 7 hari mulai berjalan
 * (kedaluwarsa bila belum disetujui admin). Tidak berpengaruh untuk vendor.
 */
trait StartsPermitValidityClock
{
    public static function bootStartsPermitValidityClock(): void
    {
        static::created(function ($permit) {
            if ($permit->notification_id) {
                app(PermitValidityService::class)->onPermitCreated((int) $permit->notification_id);
            }
        });
    }
}
