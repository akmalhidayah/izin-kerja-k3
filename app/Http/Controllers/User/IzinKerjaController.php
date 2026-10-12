<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\User\JsaController;
use App\Http\Controllers\Controller;
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
// use App\Models\WorkPermitLifesaving;
use App\Models\WorkPermitPerancah;
use App\Models\WorkPermitBeban;
use App\Models\WorkPermitListrikTeganganTinggi;
// use App\Models\WorkPermitProsedurKhusus;
use App\Models\WorkPermitDetail;
use App\Models\StepApproval;
use App\Models\Upload;
use App\Http\Controllers\User\IzinKerjaPgoController;

class IzinKerjaController extends Controller
{
public function index(Request $request)
{
    if (auth()->user()?->isPgo()) {
        return app(IzinKerjaPgoController::class)->index($request);
    }

    $userId = auth()->id();

    // Ambil semua pengajuan notifikasi milik user (buat dropdown)
    $allNotifications = Notification::where('user_id', $userId)
        ->latest()
        ->get(['id', 'number', 'created_at']);

    // Cek jika ada yang dipilih, kalau tidak pakai yang terbaru
    $selectedId = $request->get('notification_id') ?? $allNotifications->first()?->id;

    if (!$selectedId) {
        // Kalau belum pernah buat pengajuan
        $stepTitles = [
            'op_spk' => 'Buat Notifikasi/OP/SPK',
            'data_kontraktor' => 'Input Data Kontraktor',
            'bpjs' => 'Upload BPJS Ketenagakerjaan',
            'jsa' => 'Input JSA',
            'working_permit' => 'Input Working Permit',
            'fakta_integritas' => 'Upload Fakta Integritas',
            'sertifikasi_ak3' => 'Upload Sertifikasi AK3',
            'ktp' => 'Upload KTP',
            'surat_kesehatan' => 'Upload Surat Hasil MCU',
            'struktur_organisasi' => 'Upload Struktur Organisasi',
            'post_test' => 'Upload Dokumen Safety Induction',
            'bukti_serah_terima' => 'Upload BAST',
            'surat_izin_kerja' => 'Surat Penyelesaian Pekerjaan',
        ];

        $steps = [];
        foreach ($stepTitles as $code => $title) {
            $steps[] = [
                'title' => $title,
                'code' => $code,
                'status' => 'pending',
                'enabled' => false,
            ];
        }

        return view('pengajuan-user.izin-kerja', [
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
            'uploadsByStep' => collect(),
            'renderPermitFormsEagerly' => false,
        ]);
    }

    $notification = Notification::with('assignedAdmin:id,name')
        ->where('id', $selectedId)
        ->where('user_id', $userId)
        ->firstOrFail();

    // Default stepTitles semua step
    $stepTitles = [
        'op_spk' => 'Buat Notifikasi/OP/SPK',
        'data_kontraktor' => 'Input Data Kontraktor',
        'bpjs' => 'Upload BPJS Ketenagakerjaan',
        'jsa' => 'Input JSA',
        'working_permit' => 'Input Working Permit',
        'fakta_integritas' => 'Upload Fakta Integritas',
        'sertifikasi_ak3' => 'Upload Sertifikasi AK3',
        'ktp' => 'Upload KTP Pekerja',
        'surat_kesehatan' => 'Upload Surat Hasil MCU',
        'struktur_organisasi' => 'Upload Struktur Organisasi',
        'post_test' => 'Upload Dokumen Safety Induction',
        'bukti_serah_terima' => 'Upload BAST',
        'surat_izin_kerja' => 'Surat Penyelesaian Pekerjaan',
    ];

    $dataKontraktor = DataKontraktor::where('notification_id', $selectedId)->first();
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

    if ($renderPermitFormsEagerly) {
        $detailsByType = WorkPermitDetail::with('closure')
            ->where('notification_id', $selectedId)
            ->get()
            ->keyBy('permit_type');
        $detailTypeMap = ['ruang-tertutup' => 'ruangtertutup'];

        foreach ($permits as $type => $permit) {
            if (!$permit) {
                continue;
            }

            $detail = $detailsByType->get($detailTypeMap[$type] ?? $type);
            $permit->setRelation('detail', $detail);
            $permit->setRelation('closure', $detail?->closure);
        }
    }

    $approvalRecords = StepApproval::where('notification_id', $selectedId)
        ->get(['id', 'step', 'status', 'catatan', 'signature_senior_manager']);
    $firstApprovalRecords = $approvalRecords->groupBy('step')->map->first();
    $stepApprovals = $approvalRecords->pluck('status', 'step')->all();
    $stepGateStatuses = $firstApprovalRecords->pluck('status', 'step')->all();
    $stepNotes = $firstApprovalRecords->pluck('catatan', 'step')->all();
    $uploadsByStep = Upload::where('notification_id', $selectedId)
        ->get(['id', 'step', 'file_path'])
        ->groupBy('step');

    $steps = [];
    $previousApproved = true;

    foreach ($stepTitles as $code => $title) {
        $statusRaw = strtolower($stepApprovals[$code] ?? 'menunggu');
        $status = match ($statusRaw) {
            'disetujui' => 'done',
            'revisi' => 'revisi',
            default => 'pending',
        };

        // 👇 Logika override status untuk step ke-13
        if ($code === 'surat_izin_kerja') {
            $sikApproval = $firstApprovalRecords->get('sik');

            if ($sikApproval && $sikApproval->signature_senior_manager) {
                $status = 'done';
            }
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

    return view('pengajuan-user.izin-kerja', [
        'notifications' => $allNotifications,
        'selectedId' => $selectedId,
        'notification' => $notification,
        'jsa' => $jsa,
        'permits' => $permits,
        'detail' => null,
        'closure' => null,
        'dataKontraktor' => $dataKontraktor,
        'steps' => $steps,
        'generatedNoJsa' => $generatedNoJsa,
        'stepApprovals' => $stepApprovals,
        'stepGateStatuses' => $stepGateStatuses,
        'stepNotes' => $stepNotes,
        'uploadsByStep' => $uploadsByStep,
        'renderPermitFormsEagerly' => $renderPermitFormsEagerly,
    ]);
}
}
