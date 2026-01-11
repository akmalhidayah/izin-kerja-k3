<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\JsaController;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\DataKontraktor;
use App\Models\Jsa;
use App\Models\UmumWorkPermit;
use App\Models\WorkPermitGasPanas;
use App\Models\WorkPermitAir;
use App\Models\WorkPermitKetinggian;
use App\Models\WorkPermitPengangkatan;
use App\Models\WorkPermitPenggalian;
use App\Models\WorkPermitRisikoPanas;
use App\Models\WorkPermitRuangTertutup;
use App\Models\WorkPermitPerancah;
use App\Models\WorkPermitBeban;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitClosure;
use App\Models\StepApproval;

class IzinKerjaPgoController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $allNotifications = Notification::where('user_id', $userId)->latest()->get();
        $selectedId = $request->get('notification_id') ?? $allNotifications->first()?->id;

        $stepTitles = [
            'op_spk' => 'Buat Notifikasi/OP/SPK',
            'jsa' => 'Input JSA',
            'working_permit' => 'Input Working Permit',
        ];

        if (!$selectedId) {
            $steps = [];
            foreach ($stepTitles as $code => $title) {
                $steps[] = [
                    'title' => $title,
                    'code' => $code,
                    'status' => 'pending',
                    'enabled' => false,
                ];
            }

            return view('pengajuan-user.izin-kerja-pgo', [
                'notifications' => $allNotifications,
                'selectedId' => null,
                'notification' => null,
                'jsa' => null,
                'permits' => [],
                'detail' => null,
                'closure' => null,
                'dataKontraktor' => null,
                'steps' => $steps,
                'generatedNoJsa' => null,
            ]);
        }

        $notification = Notification::where('id', $selectedId)
            ->where('user_id', $userId)
            ->firstOrFail();
        $generatedNoJsa = (new JsaController)->getGeneratedNoJsa();

        $dataKontraktor = DataKontraktor::where('notification_id', $selectedId)->first();
        $jsa = Jsa::where('notification_id', $selectedId)->first();
        $permits = [
            'umum' => UmumWorkPermit::where('notification_id', $selectedId)->first(),
            'gaspanas' => WorkPermitGasPanas::where('notification_id', $selectedId)->first(),
            'air' => WorkPermitAir::where('notification_id', $selectedId)->first(),
            'ketinggian' => WorkPermitKetinggian::where('notification_id', $selectedId)->first(),
            'pengangkatan' => WorkPermitPengangkatan::where('notification_id', $selectedId)->first(),
            'penggalian' => WorkPermitPenggalian::where('notification_id', $selectedId)->first(),
            'risiko-panas' => WorkPermitRisikoPanas::where('notification_id', $selectedId)->first(),
            'ruang-tertutup' => WorkPermitRuangTertutup::where('notification_id', $selectedId)->first(),
            'beban' => WorkPermitBeban::where('notification_id', $selectedId)->first(),
            'perancah' => WorkPermitPerancah::where('notification_id', $selectedId)->first(),
        ];

        $detail = WorkPermitDetail::where('notification_id', $selectedId)->first();
        $closure = $detail ? WorkPermitClosure::where('work_permit_detail_id', $detail->id)->first() : null;

        $stepApprovals = StepApproval::where('notification_id', $selectedId)
            ->pluck('status', 'step')
            ->toArray();

        $hasOpSpk = (bool)($notification->number || $notification->file);
        $hasJsa = (bool)$jsa;
        $hasPermit = collect($permits)->filter()->isNotEmpty();

        $steps = [];
        $previousApproved = true;

        foreach ($stepTitles as $code => $title) {
            $approvalStatus = strtolower($stepApprovals[$code] ?? '');
            $hasData = match ($code) {
                'op_spk' => $hasOpSpk,
                'jsa' => $hasJsa,
                'working_permit' => $hasPermit,
                default => false,
            };

            if ($approvalStatus === 'revisi') {
                $status = 'revisi';
            } elseif ($approvalStatus === 'disetujui' || $hasData) {
                $status = 'done';
            } else {
                $status = 'pending';
            }

            $enabled = $previousApproved;
            $steps[] = [
                'title' => $title,
                'code' => $code,
                'status' => $status,
                'enabled' => $enabled,
            ];

            if ($status !== 'done') {
                $previousApproved = false;
            }
        }

        return view('pengajuan-user.izin-kerja-pgo', [
            'notifications' => $allNotifications,
            'selectedId' => $selectedId,
            'notification' => $notification,
            'jsa' => $jsa,
            'permits' => $permits,
            'detail' => $detail,
            'closure' => $closure,
            'dataKontraktor' => $dataKontraktor,
            'steps' => $steps,
            'generatedNoJsa' => $generatedNoJsa,
        ]);
    }
}
