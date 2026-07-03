<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DataKontraktor;
use App\Models\Jsa;
use App\Models\UmumWorkPermit;
use App\Models\WorkPermitAir;
use App\Models\WorkPermitBeban;
use App\Models\WorkPermitGasPanas;
use App\Models\WorkPermitKetinggian;
use App\Models\WorkPermitPengangkatan;
use App\Models\WorkPermitPenggalian;
use App\Models\WorkPermitPerancah;
use App\Models\WorkPermitRisikoPanas;
use App\Models\WorkPermitRuangTertutup;

class TokenLinkController extends Controller
{
    private array $types = [
        'data-kontraktor' => DataKontraktor::class,
        'jsa' => Jsa::class,
        'umum' => UmumWorkPermit::class,
        'gaspanas' => WorkPermitGasPanas::class,
        'air' => WorkPermitAir::class,
        'ketinggian' => WorkPermitKetinggian::class,
        'pengangkatan' => WorkPermitPengangkatan::class,
        'penggalian' => WorkPermitPenggalian::class,
        'beban' => WorkPermitBeban::class,
        'risiko-panas' => WorkPermitRisikoPanas::class,
        'ruang-tertutup' => WorkPermitRuangTertutup::class,
        'perancah' => WorkPermitPerancah::class,
    ];

    public function regenerate(string $type, int $id)
    {
        abort_unless(isset($this->types[$type]), 404);

        $record = $this->types[$type]::findOrFail($id);
        abort_unless($this->findAccessibleNotification($record->notification_id), 403);

        $this->regeneratePermitToken($record);

        return back()->with('token_regenerated', 'Token berhasil diregenerate. Link baru aktif selama ' . $this->permitTokenExpiryDays() . ' hari.');
    }
}
