<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
public function index()
{
    $notifications = \App\Models\Notification::with(['user', 'assignedAdmin'])
        ->latest()
        ->take(5)
        ->get();

    $stepTitles = [
        'op_spk', 'data_kontraktor', 'bpjs', 'jsa', 'working_permit', 'fakta_integritas',
        'sertifikasi_ak3', 'ktp', 'surat_kesehatan', 'struktur_organisasi',
        'post_test', 'sik', 'bukti_serah_terima'
    ];
$totalSteps = count($stepTitles);
    $requests = $notifications->map(function ($notif) use ($stepTitles) {
        $approvals = \App\Models\StepApproval::where('notification_id', $notif->id)->get();
        $approvedSteps = $approvals->where('status', 'disetujui')->count();
        $currentStep = min($approvedSteps + 1, count($stepTitles));
        $stepTitle = $stepTitles[$approvedSteps] ?? 'Belum Diketahui';

        // SIK file (optional)
        $sikApproval = $approvals->firstWhere('step', 'sik');
        $sikFile = $sikApproval?->file_path;

        return (object)[
            'id' => $notif->id,
            'user_name' => $notif->user->vendor_name ?? $notif->user->name ?? 'User',
            'tanggal' => $notif->created_at->format('d-m-Y H:i'),
            'handled_by' => $notif->assignedAdmin?->name ?? '-',
            'status' => match(strtolower($notif->status)) {
                'disetujui', 'selesai' => 'Terbit SIK',
                'revisi' => 'Perlu Revisi',
                default => 'Menunggu',
            },
            'progress' => round(($approvedSteps / count($stepTitles)) * 100),
            'current_step' => $currentStep,
            'current_step_title' => $stepTitle,
            'sik_file' => $sikFile,
        ];
    });

    return view('admin.dashboard', compact('requests', 'totalSteps'));
}

public function permintaanSIK(Request $request)
{
    $query = \App\Models\Notification::with(['user', 'assignedAdmin']);

    // ✅ Filter by nama user (bukan vendor)
    if ($request->filled('search')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%');
        });
    }

    // ✅ Filter Bulan & Tahun
    if ($request->filled('bulan')) {
        $query->whereMonth('created_at', $request->bulan);
    }

    if ($request->filled('tahun')) {
        $query->whereYear('created_at', $request->tahun);
    }

    $notifications = $query->latest()->paginate(10)->withQueryString();

    $stepTitles = [
        'op_spk' => 'Buat Notifikasi/OP/SPK',
        'data_kontraktor' => 'Input Data Kontraktor',
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
    $stepKeys = array_keys($stepTitles);

    $requests = $notifications->getCollection()->map(function ($notif) use ($stepTitles, $stepKeys) {
        $approvals = \App\Models\StepApproval::where('notification_id', $notif->id)->get();
        $approvedSteps = $approvals->where('status', 'disetujui')->count();
        $currentStep = min($approvedSteps + 1, count($stepTitles));
        $stepTitle = $stepTitles[$stepKeys[$approvedSteps] ?? null] ?? 'Belum Diketahui';

        return (object)[
            'id' => $notif->id,
            'user_name' => $notif->user->name ?? 'User', // 🔥 Tetap pakai name
             'number' => $notif->number, // ✅ Tambahkan ini
            'file' => $notif->file,
            'tanggal' => $notif->created_at->format('d-m-Y H:i'),
            'handled_by' => $notif->assignedAdmin?->name ?? '-',
            'status' => ucfirst($notif->status ?? 'Menunggu'),
            'progress' => round(($approvedSteps / count($stepTitles)) * 100),
            'current_step' => $currentStep,
            'current_step_title' => $stepTitle,
        ];
    });
    $notifications->setCollection($requests);

    $bulanList = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $tahunList = range(date('Y'), date('Y') - 5);
    $totalSteps = count($stepTitles); // Tambahkan ini


    return view('admin.permintaansik', [
    'requests' => $notifications, // biar blade tetap jalan
    'notifications' => $notifications, // kalau ada logic lain
    'bulanList' => $bulanList,
    'tahunList' => $tahunList,
     'totalSteps' => $totalSteps, // <-- Tambah ini
]);

}
}

