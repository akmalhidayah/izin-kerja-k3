<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\WorkingPermit\AirPermitController;
use App\Http\Controllers\User\WorkingPermit\BebanPermitController;
use App\Http\Controllers\User\WorkingPermit\GasPanasPermitController;
use App\Http\Controllers\User\WorkingPermit\KetinggianPermitController;
use App\Http\Controllers\User\WorkingPermit\PanasRisikoPermitController;
use App\Http\Controllers\User\WorkingPermit\PengangkatanPermitController;
use App\Http\Controllers\User\WorkingPermit\PenggalianPermitController;
use App\Http\Controllers\User\WorkingPermit\PerancahPermitController;
use App\Http\Controllers\User\WorkingPermit\RuangTertutupPermitController;
use App\Http\Controllers\User\WorkingPermit\UmumPermitController;
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

class TokenPdfController extends Controller
{
    private array $types = [
        'data-kontraktor' => [DataKontraktor::class, DataKontraktorController::class, 'previewPdf'],
        'jsa' => [Jsa::class, JsaController::class, 'showPdf'],
        'umum' => [UmumWorkPermit::class, UmumPermitController::class, 'preview'],
        'gaspanas' => [WorkPermitGasPanas::class, GasPanasPermitController::class, 'preview'],
        'air' => [WorkPermitAir::class, AirPermitController::class, 'preview'],
        'ketinggian' => [WorkPermitKetinggian::class, KetinggianPermitController::class, 'preview'],
        'pengangkatan' => [WorkPermitPengangkatan::class, PengangkatanPermitController::class, 'preview'],
        'penggalian' => [WorkPermitPenggalian::class, PenggalianPermitController::class, 'preview'],
        'beban' => [WorkPermitBeban::class, BebanPermitController::class, 'preview'],
        'risiko-panas' => [WorkPermitRisikoPanas::class, PanasRisikoPermitController::class, 'preview'],
        'ruang-tertutup' => [WorkPermitRuangTertutup::class, RuangTertutupPermitController::class, 'preview'],
        'perancah' => [WorkPermitPerancah::class, PerancahPermitController::class, 'preview'],
    ];

    public function show(string $type, string $token)
    {
        abort_unless(isset($this->types[$type]), 404);

        [$model, $controller, $method] = $this->types[$type];
        $record = $model::where('token', $token)->firstOrFail();
        $this->abortIfPermitTokenExpired($record);

        return app($controller)->{$method}($record->notification_id);
    }
}
