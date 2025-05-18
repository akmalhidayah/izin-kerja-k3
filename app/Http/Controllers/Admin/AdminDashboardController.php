<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
public function index()
{
    $notifications = \App\Models\Notification::with(['user', 'handledBy'])
        ->latest()
        ->take(5)
        ->get();

    $stepTitles = [
        'op_spk', 'bpjs', 'jsa', 'working_permit', 'fakta_integritas',
        'sertifikasi_ak3', 'ktp', 'surat_kesehatan', 'struktur_organisasi',
        'post_test', 'sik', 'bukti_serah_terima'
    ];

    $requests = $notifications->map(function ($notif) use ($stepTitles) {
        $approvals = \App\Models\StepApproval::where('notification_id', $notif->id)->get();
        $approvedSteps = $approvals->where('status', 'disetujui')->count();
        $currentStep = min($approvedSteps + 1, count($stepTitles));
        $stepKey = array_keys($stepTitles)[$approvedSteps] ?? null;

        // Ambil file SIK dari step_approvals step 'sik'
        $sikApproval = $approvals->firstWhere('step', 'sik');
        $sikFile = $sikApproval?->file_path;

        return (object)[
            'id' => $notif->id,
            'user_name' => $notif->user->vendor_name ?? $notif->user->name ?? 'User',
            'tanggal' => $notif->created_at->format('d-m-Y H:i'),
            'handled_by' => $notif->handledBy?->name ?? '-',
            'status' => match(strtolower($notif->status)) {
                'disetujui', 'selesai' => 'Terbit SIK',
                'revisi' => 'Perlu Revisi',
                default => 'Menunggu',
            },
            'progress' => round(($approvedSteps / count($stepTitles)) * 100),
            'current_step' => $currentStep,
            'current_step_title' => $stepTitles[$stepKey] ?? 'Belum Diketahui',
            'sik_file' => $sikFile,
        ];
    });

    return view('admin.dashboard', compact('requests'));
}


   public function permintaanSIK()
{
    $notifications = \App\Models\Notification::with(['user', 'handledBy'])->latest()->get();

    $stepTitles = [
        'op_spk' => 'Buat Notifikasi/OP/SPK',
        'bpjs' => 'Upload BPJS',
        'jsa' => 'Input JSA',
        'working_permit' => 'Input Working Permit',
        'fakta_integritas' => 'Upload Fakta Integritas',
        'sertifikasi_ak3' => 'Upload Sertifikasi AK3',
        'ktp' => 'Upload KTP Personil K3',
        'surat_kesehatan' => 'Upload Surat Kesehatan',
        'struktur_organisasi' => 'Upload Struktur Organisasi',
        'post_test' => 'Upload Post Test',
        'sik' => 'Surat Izin Kerja',
        'bukti_serah_terima' => 'Upload Bukti Serah Terima',
    ];

    $requests = $notifications->map(function ($notif) use ($stepTitles) {
        $approvals = \App\Models\StepApproval::where('notification_id', $notif->id)->get();
        $approvedSteps = $approvals->where('status', 'disetujui')->count();
        $currentStep = min($approvedSteps + 1, count($stepTitles)); // Fix di sini
        $stepKey = array_keys($stepTitles)[$approvedSteps] ?? null;

        return (object)[
            'id' => $notif->id,
            'user_name' => $notif->user->name ?? 'User',
            'vendor_name' => $notif->user->vendor_name ?? '-', // tambahkan vendor_name jika ada
            'tanggal' => $notif->created_at->format('d-m-Y H:i'),
            'handled_by' => $notif->handledBy?->name ?? '-', // fix: tampilkan default '-'
            'status' => ucfirst($notif->status ?? 'Menunggu'),
            'progress' => round(($approvedSteps / count($stepTitles)) * 100),
            'current_step' => $currentStep,
            'current_step_title' => $stepTitles[$stepKey] ?? 'Belum Diketahui',
        ];
    });

    return view('admin.permintaansik', compact('requests'));
}

}

