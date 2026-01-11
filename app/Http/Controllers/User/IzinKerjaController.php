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
// use App\Models\WorkPermitProsedurKhusus;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitClosure;
use App\Models\StepApproval;

class IzinKerjaController extends Controller
{
   public function index(Request $request)
{
    $userId = auth()->id();

    // Ambil semua pengajuan notifikasi milik user (buat dropdown)
    $allNotifications = Notification::where('user_id', $userId)->latest()->get();

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
            'surat_izin_kerja' => 'Surat Izin Kerja',
        ];

        // Cek usertype saat belum ada pengajuan juga
        if (auth()->user()->usertype === 'pgo') {
            // Filter step untuk PGO
            $stepTitles = [
                'op_spk' => 'Buat Notifikasi/OP/SPK',
                'jsa' => 'Input JSA',
                'working_permit' => 'Input Working Permit',
            ];
        }

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
        ]);
    }

    $notification = Notification::findOrFail($selectedId);
    $generatedNoJsa = (new JsaController)->getGeneratedNoJsa();

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
        'surat_izin_kerja' => 'Surat Izin Kerja',
    ];

    // **Cek usertype setelah ambil $notification**
    if (auth()->user()->usertype === 'pgo') {
        // Step khusus usertype PGO
        $stepTitles = [
            'op_spk' => 'Buat Notifikasi/OP/SPK',
            'jsa' => 'Input JSA',
            'working_permit' => 'Input Working Permit',
        ];
    }

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

    $stepApprovals = StepApproval::where('notification_id', $selectedId)->pluck('status', 'step')->toArray();

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
            $sikApproval = StepApproval::where('notification_id', $selectedId)
                ->where('step', 'sik')
                ->first();

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
        'detail' => $detail,
        'closure' => $closure,
        'dataKontraktor' => $dataKontraktor,
        'steps' => $steps,
        'generatedNoJsa' => $generatedNoJsa,
    ]);
}
}
