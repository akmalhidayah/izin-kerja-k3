<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
public function index()
{
    // ðŸ”¸ Untuk summary (tanpa filter bulan/tahun)
    $allNotifications = \App\Models\Notification::with(['user', 'assignedAdmin'])->latest()->get();

    $stepTitles = [
        'op_spk', 'data_kontraktor', 'bpjs', 'jsa', 'working_permit', 'fakta_integritas',
        'sertifikasi_ak3', 'ktp', 'surat_kesehatan', 'struktur_organisasi',
        'post_test', 'sik', 'bukti_serah_terima'
    ];
    $totalSteps = count($stepTitles);

$summaryRequests = $allNotifications->map(function ($notif) use ($stepTitles) {
    $approvals = \App\Models\StepApproval::where('notification_id', $notif->id)->get();
    $approvedSteps = $approvals->where('status', 'disetujui')->count();
    $currentStep = min($approvedSteps + 1, count($stepTitles));
    $stepTitle = $stepTitles[$approvedSteps] ?? 'Belum Diketahui';

    $sikApproval = $approvals->firstWhere('step', 'sik');
    $sikFile = $sikApproval?->file_path;

    // 🔧 Update di sini:
$status = match (true) {
    $approvals->where('status', 'revisi')->isNotEmpty() && $approvals->where('status', 'disetujui')->count() < count($stepTitles)
        => 'Perlu Revisi',

    in_array(strtolower($notif->status), ['disetujui', 'selesai'])
        => 'Terbit SIK',

    default => 'Menunggu',
};


    return (object)[
        'id' => $notif->id,
        'user_name' => $notif->user->vendor_name ?? $notif->user->name ?? 'User',
        'tanggal' => $notif->created_at->format('d-m-Y H:i'),
        'handled_by' => $notif->assignedAdmin?->name ?? '-',
        'status' => $status,
        'progress' => round(($approvedSteps / count($stepTitles)) * 100),
        'current_step' => $currentStep,
        'current_step_title' => $stepTitle,
        'sik_file' => $sikFile,
        'created_at' => $notif->created_at
    ];
});

    // ðŸ”¸ Filter yang sudah ada SIK dan bulan ini untuk tabel saja
    $filteredRequests = $summaryRequests->filter(function ($req) {
        return $req->sik_file &&
               $req->created_at->format('Y-m') === now()->format('Y-m');
    });

    return view('admin.dashboard', [
        'requests' => $filteredRequests,
        'summaryRequests' => $summaryRequests,
        'totalSteps' => $totalSteps,
    ]);
}


public function permintaanSIK(Request $request)
{
    $query = \App\Models\Notification::with(['user', 'assignedAdmin']);

    // 🔎 Filter berdasarkan nama user
    if ($request->filled('search')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%');
        });
    }

    // 📅 Filter Bulan dan Tahun
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
    $totalSteps = count($stepTitles);

    $requests = $notifications->getCollection()->map(function ($notif) use ($stepTitles, $stepKeys, $totalSteps) {
        $approvals = \App\Models\StepApproval::where('notification_id', $notif->id)->get();
        $approvedSteps = $approvals->where('status', 'disetujui')->count();
        $currentStep = min($approvedSteps + 1, $totalSteps);
        $currentStepKey = $stepKeys[$approvedSteps] ?? null;
        $stepTitle = $stepTitles[$currentStepKey] ?? 'Belum Diketahui';

        // ✅ Cek status berdasarkan step saat ini
        $currentStepStatus = $approvals->firstWhere('step', $currentStepKey)?->status ?? 'menunggu';
        $status = match ($currentStepStatus) {
            'disetujui' => 'Disetujui',
            'revisi' => 'Perlu Revisi',
            default => 'Menunggu',
        };

        return (object)[
            'id' => $notif->id,
            'user_name' => $notif->user->name ?? 'User',
            'number' => $notif->number,
            'file' => $notif->file,
            'tanggal' => $notif->created_at->format('d-m-Y H:i'),
            'handled_by' => $notif->assignedAdmin?->name ?? '-',
            'status' => $currentStep >= $totalSteps ? 'Selesai' : $status,
            'progress' => round(($approvedSteps / $totalSteps) * 100),
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

    return view('admin.permintaansik', [
        'requests' => $notifications,
        'notifications' => $notifications,
        'bulanList' => $bulanList,
        'tahunList' => $tahunList,
        'totalSteps' => $totalSteps,
    ]);
}

}

 