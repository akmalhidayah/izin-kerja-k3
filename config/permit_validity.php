<?php

return [
    /*
     * Masa berlaku Working Permit (umum + khusus) dalam hari.
     * Vendor: dihitung sejak admin menyetujui step working_permit, sampai step SIK disetujui.
     * PGO: dihitung sejak permit dibuat; bila belum disetujui admin dalam batas ini, permit kedaluwarsa.
     */
    'days' => (int) env('PERMIT_VALID_DAYS', 7),
];
