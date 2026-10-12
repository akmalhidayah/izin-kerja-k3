<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\JsaController;
use Illuminate\Http\Request;
use App\Models\Notification;
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
use App\Models\WorkPermitListrikTeganganTinggi;
use App\Models\WorkPermitDetail;
use App\Models\StepApproval;

class IzinKerjaPgoController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        // Ambil semua pengajuan
        $allNotifications = Notification::where('user_id', $userId)
            ->latest()
            ->get(['id', 'number', 'created_at']);
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
                'stepApprovals' => [],
                'stepGateStatuses' => [],
                'stepNotes' => [],
                'renderPermitFormsEagerly' => false,
            ]);
        }

        // Ambil data utama
        $notification = Notification::with('assignedAdmin:id,name')
            ->where('id', $selectedId)
            ->where('user_id', $userId)
            ->firstOrFail();

        // Data terkait
        $jsa = Jsa::where('notification_id', $selectedId)->first();
        $generatedNoJsa = $jsa ? null : (new JsaController)->getGeneratedNoJsa();
        $renderPermitFormsEagerly = $request->session()->getOldInput() !== [];
        $permitColumns = $renderPermitFormsEagerly
            ? ['*']
            : ['id', 'notification_id', 'token', 'token_expires_at'];

        $permits = [
            'umum' => UmumWorkPermit::where('notification_id', $selectedId)->first($permitColumns),
            'gaspanas' => WorkPermitGasPanas::where('notification_id', $selectedId)->first($permitColumns),
            'air' => WorkPermitAir::where('notification_id', $selectedId)->first($permitColumns),
            'ketinggian' => WorkPermitKetinggian::where('notification_id', $selectedId)->first($permitColumns),
            'pengangkatan' => WorkPermitPengangkatan::where('notification_id', $selectedId)->first($permitColumns),
            'penggalian' => WorkPermitPenggalian::where('notification_id', $selectedId)->first($permitColumns),
            'risiko-panas' => WorkPermitRisikoPanas::where('notification_id', $selectedId)->first($permitColumns),
            'ruang-tertutup' => WorkPermitRuangTertutup::where('notification_id', $selectedId)->first($permitColumns),
            'beban' => WorkPermitBeban::where('notification_id', $selectedId)->first($permitColumns),
            'perancah' => WorkPermitPerancah::where('notification_id', $selectedId)->first($permitColumns),
            'listrik-tegangan-tinggi' => WorkPermitListrikTeganganTinggi::where('notification_id', $selectedId)->first($permitColumns),
        ];

        $detail = null;
        $closure = null;
        if ($renderPermitFormsEagerly) {
            $detailsByType = WorkPermitDetail::with('closure')
                ->where('notification_id', $selectedId)
                ->get()
                ->keyBy('permit_type');
            $detail = $detailsByType->first();
            $closure = $detail?->closure;
            $detailTypeMap = ['ruang-tertutup' => 'ruangtertutup'];

            foreach ($permits as $type => $permit) {
                if (!$permit) {
                    continue;
                }

                $permitDetail = $detailsByType->get($detailTypeMap[$type] ?? $type);
                $permit->setRelation('detail', $permitDetail);
                $permit->setRelation('closure', $permitDetail?->closure);
            }
        }

        // Ambil approval status dari admin
        $approvalRecords = StepApproval::where('notification_id', $selectedId)
            ->get(['step', 'status', 'catatan'])
            ->keyBy('step');
        $stepApprovals = $approvalRecords->pluck('status', 'step')->all();
        $stepNotes = $approvalRecords->pluck('catatan', 'step')->all();

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
            'dataKontraktor' => null,
            'steps' => $steps,
            'generatedNoJsa' => $generatedNoJsa,
            'stepNotes' => $stepNotes,
            'stepApprovals' => $stepApprovals,
            'stepGateStatuses' => $stepApprovals,
            'renderPermitFormsEagerly' => $renderPermitFormsEagerly,
        ]);
    }
}
