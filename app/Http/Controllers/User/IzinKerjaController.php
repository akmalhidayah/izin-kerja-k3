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
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitClosure;
use App\Models\StepApproval;

class IzinKerjaController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $notification = Notification::where('user_id', $userId)->latest()->first();
$generatedNoJsa = (new JsaController)->getGeneratedNoJsa();


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

        // Jika belum ada notifikasi, tampilkan semua step sebagai pending
        if (!$notification) {
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

        // Data permit terkait
        $dataKontraktor = DataKontraktor::where('notification_id', $notification->id)->first();
        $jsa = Jsa::where('notification_id', $notification->id)->first();
        $permits = [
            'umum' => UmumWorkPermit::where('notification_id', $notification->id)->first(),
            'gaspanas' => WorkPermitGasPanas::where('notification_id', $notification->id)->first(),
            'air' => WorkPermitAir::where('notification_id', $notification->id)->first(),
        ];
        $detail = WorkPermitDetail::where('notification_id', $notification->id)->first();
        $closure = $detail ? WorkPermitClosure::where('work_permit_detail_id', $detail->id)->first() : null;

        // Ambil status approval dari DB
        $stepApprovals = StepApproval::where('notification_id', $notification->id)
            ->pluck('status', 'step')
            ->toArray();

        // Build steps dengan logic chaining
        $steps = [];
        $previousApproved = true; // Step 1 always enabled

        foreach ($stepTitles as $code => $title) {
            $statusRaw = strtolower($stepApprovals[$code] ?? 'menunggu');
            $status = match ($statusRaw) {
                'disetujui' => 'done',
                'revisi' => 'revisi',
                default => 'pending',
            };

            $enabled = $previousApproved;
            $steps[] = [
                'title' => $title,
                'code' => $code,
                'status' => $status,
                'enabled' => $enabled,
            ];

            // Kalau step ini belum disetujui, step berikutnya disable
            if ($status !== 'done') {
                $previousApproved = false;
            }
        }

        return view('pengajuan-user.izin-kerja', compact(
            'notification', 'jsa', 'permits', 'detail', 'closure', 'dataKontraktor', 'steps',  'generatedNoJsa'
        ));
    }
}
