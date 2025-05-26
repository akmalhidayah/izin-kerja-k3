<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
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

        // Default step titles
        $stepTitles = [
            'op_spk' => 'Buat Notifikasi/OP/SPK',
            'bpjs' => 'Upload BPJS Ketenagakerjaan',
            'jsa' => 'Input JSA',
            'working_permit' => 'Input Working Permit',
            'fakta_integritas' => 'Upload Fakta Integritas',
            'sertifikasi_ak3' => 'Upload Sertifikasi AK3',
            'ktp' => 'Upload KTP',
            'surat_kesehatan' => 'Upload Surat Kesehatan',
            'struktur_organisasi' => 'Upload Struktur Organisasi',
            'post_test' => 'Upload Post Test',
            'surat_izin_kerja' => 'Surat Izin Kerja',
            'bukti_serah_terima' => 'Upload Bukti Serah Terima',
        ];

        // Kalau belum ada pengajuan, tampilkan semua step default sebagai pending
        if (!$notification) {
            $steps = [];
            foreach ($stepTitles as $code => $title) {
                $steps[] = [
                    'title' => $title,
                    'code' => $code,
                    'status' => 'pending',
                ];
            }

            return view('pengajuan-user.izin-kerja', [
                'notification' => null,
                'jsa' => null,
                'permits' => [],
                'detail' => null,
                'closure' => null,
                'steps' => $steps,
            ]);
        }

        // Data terkait notifikasi
        $jsa = Jsa::where('notification_id', $notification->id)->first();

        $permits = [
            'umum' => UmumWorkPermit::where('notification_id', $notification->id)->first(),
            'gaspanas' => WorkPermitGasPanas::where('notification_id', $notification->id)->first(),
            'air' => WorkPermitAir::where('notification_id', $notification->id)->first(),
        ];

        $detail = WorkPermitDetail::where('notification_id', $notification->id)->first();
        $closure = $detail ? WorkPermitClosure::where('work_permit_detail_id', $detail->id)->first() : null;

        // Ambil status step dari StepApproval
        $stepApprovals = StepApproval::where('notification_id', $notification->id)
            ->pluck('status', 'step')
            ->toArray();

        // Format data steps untuk frontend
        $steps = [];
        foreach ($stepTitles as $code => $title) {
            $statusRaw = strtolower($stepApprovals[$code] ?? 'menunggu');
            $status = match ($statusRaw) {
                'disetujui' => 'done',
                'revisi' => 'revisi',
                default => 'pending',
            };

            $steps[] = [
                'title' => $title,
                'code' => $code,
                'status' => $status,
            ];
        }

        return view('pengajuan-user.izin-kerja', compact(
            'notification', 'jsa', 'permits', 'detail', 'closure', 'steps'
        ));
    }
}
