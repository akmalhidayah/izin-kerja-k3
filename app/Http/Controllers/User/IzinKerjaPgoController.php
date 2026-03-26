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

        // Ambil semua pengajuan
        $allNotifications = Notification::where('user_id', $userId)->latest()->get();
        $selectedId = $request->get('notification_id') ?? $allNotifications->first()?->id;

        // Step khusus PGO (3 step saja)
        $stepTitles = [
            'op_spk' => 'Buat Notifikasi/OP/SPK',
            'jsa' => 'Input JSA',
            'working_permit' => 'Input Working Permit',
        ];

        // Jika belum ada pengajuan sama sekali
        if (!$selectedId) {
            $steps = collect($stepTitles)->map(function ($title, $code) {
                return [
                    'title' => $title,
                    'code' => $code,
                    'status' => 'pending',
                    'enabled' => false,
                ];
            })->values();

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

        // Ambil data utama
        $notification = Notification::where('id', $selectedId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $generatedNoJsa = (new JsaController)->getGeneratedNoJsa();

        // Data terkait
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

        // Ambil approval status dari admin
        $stepApprovals = StepApproval::where('notification_id', $selectedId)
            ->pluck('status', 'step')
            ->toArray();
        $stepNotes = StepApproval::where('notification_id', $selectedId)
    ->pluck('catatan', 'step')
    ->toArray();

        // 🔥 BUILD STEP (FIX TOTAL)
        $steps = [];
        $previousApproved = true;

        foreach ($stepTitles as $code => $title) {

            $approvalStatus = strtolower($stepApprovals[$code] ?? 'menunggu');

            // ✅ Status hanya berdasarkan approval admin
            $status = match ($approvalStatus) {
                'disetujui' => 'done',
                'revisi' => 'revisi',
                default => 'pending',
            };

            // ✅ Step hanya aktif jika sebelumnya sudah selesai
            $enabled = $previousApproved;

            $steps[] = [
                'title' => $title,
                'code' => $code,
                'status' => $status,
                'enabled' => $enabled,
            ];

            // 🔒 Lock step berikutnya
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
            'stepNotes' => $stepNotes,
        ]);
    }
}