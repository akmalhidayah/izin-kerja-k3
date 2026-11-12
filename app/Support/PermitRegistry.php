<?php

namespace App\Support;

use App\Http\Controllers\User\WorkingPermit\AirPermitController;
use App\Http\Controllers\User\WorkingPermit\BebanPermitController;
use App\Http\Controllers\User\WorkingPermit\GasPanasPermitController;
use App\Http\Controllers\User\WorkingPermit\KetinggianPermitController;
use App\Http\Controllers\User\WorkingPermit\ListrikTeganganTinggiPermitController;
use App\Http\Controllers\User\WorkingPermit\PanasRisikoPermitController;
use App\Http\Controllers\User\WorkingPermit\PengangkatanPermitController;
use App\Http\Controllers\User\WorkingPermit\PenggalianPermitController;
use App\Http\Controllers\User\WorkingPermit\PerancahPermitController;
use App\Http\Controllers\User\WorkingPermit\RuangTertutupPermitController;
use App\Http\Controllers\User\WorkingPermit\UmumPermitController;
use App\Models\UmumWorkPermit;
use App\Models\WorkPermitAir;
use App\Models\WorkPermitBeban;
use App\Models\WorkPermitClosure;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitGasPanas;
use App\Models\WorkPermitKetinggian;
use App\Models\WorkPermitListrikTeganganTinggi;
use App\Models\WorkPermitPengangkatan;
use App\Models\WorkPermitPenggalian;
use App\Models\WorkPermitPerancah;
use App\Models\WorkPermitRisikoPanas;
use App\Models\WorkPermitRuangTertutup;
use Illuminate\Database\Eloquent\Model;

/**
 * Daftar jenis Working Permit untuk fitur masa berlaku & arsip.
 *
 * Kunci mengikuti kunci user/token (mis. "ruang-tertutup"); "detail_type" adalah nilai
 * work_permit_details.permit_type (legacy "ruangtertutup"); null = permit tanpa detail/closure.
 * Peta permit lain di aplikasi masih terduplikasi (lihat docs/CODEMAP.md bagian 6).
 */
class PermitRegistry
{
    public const TYPES = [
        'umum' => ['label' => 'Umum', 'model' => UmumWorkPermit::class, 'controller' => UmumPermitController::class, 'detail_type' => 'umum'],
        'gaspanas' => ['label' => 'Gas Panas', 'model' => WorkPermitGasPanas::class, 'controller' => GasPanasPermitController::class, 'detail_type' => 'gaspanas'],
        'air' => ['label' => 'Air', 'model' => WorkPermitAir::class, 'controller' => AirPermitController::class, 'detail_type' => 'air'],
        'ketinggian' => ['label' => 'Ketinggian', 'model' => WorkPermitKetinggian::class, 'controller' => KetinggianPermitController::class, 'detail_type' => 'ketinggian'],
        'pengangkatan' => ['label' => 'Pengangkatan', 'model' => WorkPermitPengangkatan::class, 'controller' => PengangkatanPermitController::class, 'detail_type' => null],
        'penggalian' => ['label' => 'Penggalian', 'model' => WorkPermitPenggalian::class, 'controller' => PenggalianPermitController::class, 'detail_type' => 'penggalian'],
        'risiko-panas' => ['label' => 'Risiko Panas', 'model' => WorkPermitRisikoPanas::class, 'controller' => PanasRisikoPermitController::class, 'detail_type' => 'risiko-panas'],
        'ruang-tertutup' => ['label' => 'Ruang Tertutup', 'model' => WorkPermitRuangTertutup::class, 'controller' => RuangTertutupPermitController::class, 'detail_type' => 'ruangtertutup'],
        'beban' => ['label' => 'Beban', 'model' => WorkPermitBeban::class, 'controller' => BebanPermitController::class, 'detail_type' => 'beban'],
        'perancah' => ['label' => 'Perancah', 'model' => WorkPermitPerancah::class, 'controller' => PerancahPermitController::class, 'detail_type' => 'perancah'],
        'listrik-tegangan-tinggi' => ['label' => 'Listrik Tegangan Tinggi', 'model' => WorkPermitListrikTeganganTinggi::class, 'controller' => ListrikTeganganTinggiPermitController::class, 'detail_type' => null],
    ];

    public static function label(string $type): string
    {
        return self::TYPES[$type]['label'] ?? ucwords(str_replace(['-', '_'], ' ', $type));
    }

    public function get(string $type): array
    {
        return self::TYPES[$type] ?? throw new \InvalidArgumentException("Jenis permit tidak dikenal: {$type}");
    }

    /** @return array<string, Model> permit yang ada untuk pengajuan ini, dengan kunci jenis permit */
    public function existingPermits(int $notificationId): array
    {
        $permits = [];

        foreach (self::TYPES as $type => $definition) {
            $permit = $definition['model']::where('notification_id', $notificationId)->first();

            if ($permit) {
                $permits[$type] = $permit;
            }
        }

        return $permits;
    }

    /** @return array{0: ?WorkPermitDetail, 1: ?WorkPermitClosure} */
    public function detailAndClosure(string $type, int $notificationId): array
    {
        $detailType = $this->get($type)['detail_type'];

        if ($detailType === null) {
            return [null, null];
        }

        $detail = WorkPermitDetail::where('notification_id', $notificationId)
            ->where('permit_type', $detailType)
            ->first();
        $closure = $detail
            ? WorkPermitClosure::where('work_permit_detail_id', $detail->id)->first()
            : null;

        return [$detail, $closure];
    }

    public function findByToken(string $token): ?Model
    {
        foreach (self::TYPES as $definition) {
            $record = $definition['model']::where('token', $token)->first();

            if ($record) {
                return $record;
            }
        }

        return null;
    }
}
