<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UmumWorkPermit;
use App\Models\WorkPermitAir;
use App\Models\WorkPermitBeban;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitGasPanas;
use App\Models\WorkPermitKetinggian;
use App\Models\WorkPermitListrikTeganganTinggi;
use App\Models\WorkPermitPengangkatan;
use App\Models\WorkPermitPenggalian;
use App\Models\WorkPermitPerancah;
use App\Models\WorkPermitRisikoPanas;
use App\Models\WorkPermitRuangTertutup;
use Illuminate\Http\Request;

class WorkingPermitFormController extends Controller
{
    private const PERMITS = [
        'umum' => [UmumWorkPermit::class, 'components.permits.umum', 'umum'],
        'gaspanas' => [WorkPermitGasPanas::class, 'components.permits.gaspanas', 'gaspanas'],
        'air' => [WorkPermitAir::class, 'components.permits.air', 'air'],
        'ketinggian' => [WorkPermitKetinggian::class, 'components.permits.ketinggian', 'ketinggian'],
        'pengangkatan' => [WorkPermitPengangkatan::class, 'components.permits.pengangkatan', null],
        'penggalian' => [WorkPermitPenggalian::class, 'components.permits.penggalian', 'penggalian'],
        'risiko-panas' => [WorkPermitRisikoPanas::class, 'components.permits.risiko-panas', 'risiko-panas'],
        'panas-risiko' => [WorkPermitRisikoPanas::class, 'components.permits.risiko-panas', 'risiko-panas'],
        'ruang-tertutup' => [WorkPermitRuangTertutup::class, 'components.permits.ruang-tertutup', 'ruangtertutup'],
        'beban' => [WorkPermitBeban::class, 'components.permits.beban', 'beban'],
        'perancah' => [WorkPermitPerancah::class, 'components.permits.perancah', 'perancah'],
        'listrik-tegangan-tinggi' => [WorkPermitListrikTeganganTinggi::class, 'components.permits.listrik-tegangan-tinggi', null],
    ];

    public function show(Request $request, int $notification, string $type)
    {
        abort_unless(isset(self::PERMITS[$type]), 404);

        $notificationModel = $this->findAccessibleNotification($notification);
        abort_unless($notificationModel, 403, 'Akses ditolak.');

        [$modelClass, $view, $detailType] = self::PERMITS[$type];
        $permit = $modelClass::where('notification_id', $notificationModel->id)->first();
        $context = $request->query('context') === 'add' ? 'add' : 'edit';

        [$detail, $closure] = $this->resolveFormData(
            $notificationModel->id,
            $type,
            $detailType,
            $context
        );

        return response()->view($view, [
            'notification' => $notificationModel,
            'permit' => $permit,
            'permitBeban' => $type === 'beban' ? $permit : null,
            'detail' => $detail,
            'closure' => $closure,
            'signaturePrefix' => "lazy-{$context}-{$type}-".($permit?->id ?? 'new'),
        ])->header('Cache-Control', 'no-store');
    }

    private function resolveFormData(
        int $notificationId,
        string $type,
        ?string $detailType,
        string $context
    ): array {
        if ($detailType === null) {
            return [null, null];
        }

        if (auth()->user()?->isPgo() && $context === 'edit' && $type !== 'umum') {
            $detail = WorkPermitDetail::with('closure')
                ->where('notification_id', $notificationId)
                ->first();

            return [$detail, $detail?->closure];
        }

        $detail = WorkPermitDetail::with('closure')
            ->where('notification_id', $notificationId)
            ->where('permit_type', $detailType)
            ->first();

        if ($detail || $context === 'add' || $type === 'umum') {
            if ($context !== 'edit' || $type === 'umum' || $detail?->closure) {
                return [$detail, $detail?->closure];
            }
        }

        $generalDetail = WorkPermitDetail::with('closure')
            ->where('notification_id', $notificationId)
            ->where('permit_type', 'umum')
            ->first();

        return [
            $detail ?? $generalDetail,
            $detail?->closure ?? $generalDetail?->closure,
        ];
    }
}
