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
        'user_jabatan' => $notif->user->jabatan ?? '-',
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
    $query = \App\Models\Notification::with(['user', 'assignedAdmin'])
        ->whereHas('user', fn($q) => $q->where('usertype', '!=', 'pgo'));

    // 🔍 Pencarian bebas
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('number', 'like', '%' . $request->search . '%')
              ->orWhere('type', 'like', '%' . $request->search . '%')
              ->orWhere('description', 'like', '%' . $request->search . '%')
              ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', '%' . $request->search . '%'));
        });
    }

    // 👤 Filter Admin Penanggung Jawab
    if ($request->filled('admin')) {
        $query->whereHas('assignedAdmin', fn($q) => $q->where('name', 'like', '%' . $request->admin . '%'));
    }
// 📄 Filter berdasarkan status dokumen
if ($request->filled('status')) {
    $status = strtolower($request->status);

    $query->whereHas('stepApprovals', function ($q) use ($status) {
        if ($status === 'selesai') {
            $q->where('step', 'bukti_serah_terima')->where('status', 'disetujui');
        } elseif ($status === 'disetujui') {
            $q->where('status', 'disetujui');
        } elseif ($status === 'perlu revisi') {
            $q->where('status', 'revisi');
        } elseif ($status === 'menunggu') {
            $q->where('status', '!=', 'disetujui')
              ->where('status', '!=', 'revisi');
        }
    });
}

    // 📅 Filter Rentang Bulan & Tahun
    if ($request->filled('bulan_dari') && $request->filled('tahun_dari') &&
        $request->filled('bulan_sampai') && $request->filled('tahun_sampai')) {
        $from = \Carbon\Carbon::createFromDate($request->tahun_dari, $request->bulan_dari, 1)->startOfMonth();
        $to = \Carbon\Carbon::createFromDate($request->tahun_sampai, $request->bulan_sampai, 1)->endOfMonth();
        $query->whereBetween('created_at', [$from, $to]);
    }

    // 📌 Ambil data utama
    $notifications = $query->latest()->paginate(10)->withQueryString();

    // 🔢 Step Titles
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
            'bukti_serah_terima' => 'Upload Bukti Serah Terima',
            'sik' => 'Surat Izin Kerja',
        ];
    $stepKeys = array_keys($stepTitles);
    $totalSteps = count($stepTitles);

    // 🧠 Proses mapping
    $requests = $notifications->getCollection()->map(function ($notif) use ($stepTitles, $stepKeys, $totalSteps) {
        $approvals = \App\Models\StepApproval::where('notification_id', $notif->id)->get();
        $approvalMap = $approvals->pluck('status', 'step');

        $hasOpSpk = (bool)($notif->number || $notif->file);
        $hasJsa = \App\Models\Jsa::where('notification_id', $notif->id)->exists();
        $hasPermit = false;
        $permitModels = [
            \App\Models\UmumWorkPermit::class,
            \App\Models\WorkPermitGasPanas::class,
            \App\Models\WorkPermitAir::class,
            \App\Models\WorkPermitKetinggian::class,
            \App\Models\WorkPermitPengangkatan::class,
            \App\Models\WorkPermitPenggalian::class,
            \App\Models\WorkPermitRisikoPanas::class,
            \App\Models\WorkPermitRuangTertutup::class,
            \App\Models\WorkPermitPerancah::class,
            \App\Models\WorkPermitBeban::class,
        ];
        foreach ($permitModels as $model) {
            if ($model::where('notification_id', $notif->id)->exists()) {
                $hasPermit = true;
                break;
            }
        }

        $stepStatus = [];
        foreach ($stepKeys as $stepKey) {
            $approvalStatus = strtolower($approvalMap[$stepKey] ?? '');
            $hasData = match ($stepKey) {
                'op_spk' => $hasOpSpk,
                'jsa' => $hasJsa,
                'working_permit' => $hasPermit,
                default => false,
            };

            if ($approvalStatus === 'revisi') {
                $stepStatus[$stepKey] = 'revisi';
            } elseif ($approvalStatus === 'disetujui' || $hasData) {
                $stepStatus[$stepKey] = 'disetujui';
            } else {
                $stepStatus[$stepKey] = 'menunggu';
            }
        }

        $approvedSteps = collect($stepStatus)
            ->filter(fn ($value) => $value === 'disetujui')
            ->count();
        $currentStep = min($approvedSteps + 1, $totalSteps);
        $currentStepKey = $stepKeys[$approvedSteps] ?? null;
        $stepTitle = $stepTitles[$currentStepKey] ?? 'Belum Diketahui';

        $hasRevisi = in_array('revisi', $stepStatus, true);
        $currentStatus = match (true) {
            $hasRevisi => 'Perlu Revisi',
            $approvedSteps >= $totalSteps => 'Selesai',
            $approvedSteps > 0 => 'Disetujui',
            default => 'Menunggu',
        };

        return (object)[
            'id' => $notif->id,
            'user_name' => $notif->user->name ?? '-',
            'user_jabatan' => $notif->user->jabatan ?? '-',
            'number' => $notif->number,
            'file' => $notif->file,
            'tanggal' => $notif->created_at->format('d-m-Y H:i'),
            'handled_by' => $notif->assignedAdmin?->name ?? '-',
            'status' => $currentStatus,
            'progress' => round(($approvedSteps / $totalSteps) * 100),
            'current_step' => $currentStep,
            'current_step_title' => $stepTitle,
            'approved_steps' => $approvedSteps,
            'is_completed' => $approvedSteps >= $totalSteps,
        ];
    });

    $notifications->setCollection($requests);

    // 📋 Admin, Bulan, Tahun List
    $adminList = \App\Models\User::whereHas('handledNotifications')->pluck('name', 'id');
    $bulanList = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $tahunList = range(date('Y'), date('Y') - 5);

    return view('admin.permintaansik', [
        'requests' => $notifications,
        'notifications' => $notifications,
        'adminList' => $adminList,
        'bulanList' => $bulanList,
        'tahunList' => $tahunList,
        'totalSteps' => $totalSteps,
    ]);
}

public function permintaanSIKPgo(Request $request)
{
    $query = \App\Models\Notification::with(['user', 'assignedAdmin'])
        ->whereHas('user', fn($q) => $q->where('usertype', 'pgo'));

    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('number', 'like', '%' . $request->search . '%')
                ->orWhere('type', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%')
                ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', '%' . $request->search . '%'));
        });
    }

    if ($request->filled('admin')) {
        $query->whereHas('assignedAdmin', fn($q) => $q->where('name', 'like', '%' . $request->admin . '%'));
    }

    if ($request->filled('status')) {
        $status = strtolower($request->status);

        $query->whereHas('stepApprovals', function ($q) use ($status) {
            if ($status === 'selesai') {
                $q->where('step', 'working_permit')->where('status', 'disetujui');
            } elseif ($status === 'disetujui') {
                $q->where('status', 'disetujui');
            } elseif ($status === 'perlu revisi') {
                $q->where('status', 'revisi');
            } elseif ($status === 'menunggu') {
                $q->where('status', '!=', 'disetujui')
                    ->where('status', '!=', 'revisi');
            }
        });
    }

    if ($request->filled('bulan_dari') && $request->filled('tahun_dari') &&
        $request->filled('bulan_sampai') && $request->filled('tahun_sampai')) {
        $from = \Carbon\Carbon::createFromDate($request->tahun_dari, $request->bulan_dari, 1)->startOfMonth();
        $to = \Carbon\Carbon::createFromDate($request->tahun_sampai, $request->bulan_sampai, 1)->endOfMonth();
        $query->whereBetween('created_at', [$from, $to]);
    }

    $notifications = $query->latest()->paginate(10)->withQueryString();

    $stepTitles = [
        'op_spk' => 'Buat Notifikasi/OP/SPK',
        'jsa' => 'Input JSA',
        'working_permit' => 'Input Working Permit',
    ];
    $stepKeys = array_keys($stepTitles);
    $totalSteps = count($stepTitles);

    $requests = $notifications->getCollection()->map(function ($notif) use ($stepTitles, $stepKeys, $totalSteps) {
        $approvals = \App\Models\StepApproval::where('notification_id', $notif->id)->get();
        $approvalMap = $approvals->pluck('status', 'step');

        $hasOpSpk = (bool)($notif->number || $notif->file);
        $hasJsa = \App\Models\Jsa::where('notification_id', $notif->id)->exists();
        $hasPermit = false;
        $permitModels = [
            \App\Models\UmumWorkPermit::class,
            \App\Models\WorkPermitGasPanas::class,
            \App\Models\WorkPermitAir::class,
            \App\Models\WorkPermitKetinggian::class,
            \App\Models\WorkPermitPengangkatan::class,
            \App\Models\WorkPermitPenggalian::class,
            \App\Models\WorkPermitRisikoPanas::class,
            \App\Models\WorkPermitRuangTertutup::class,
            \App\Models\WorkPermitPerancah::class,
            \App\Models\WorkPermitBeban::class,
        ];
        foreach ($permitModels as $model) {
            if ($model::where('notification_id', $notif->id)->exists()) {
                $hasPermit = true;
                break;
            }
        }

        $stepStatus = [];
        foreach ($stepKeys as $stepKey) {
            $approvalStatus = strtolower($approvalMap[$stepKey] ?? '');
            $hasData = match ($stepKey) {
                'op_spk' => $hasOpSpk,
                'jsa' => $hasJsa,
                'working_permit' => $hasPermit,
                default => false,
            };

            if ($approvalStatus === 'revisi') {
                $stepStatus[$stepKey] = 'revisi';
            } elseif ($approvalStatus === 'disetujui' || $hasData) {
                $stepStatus[$stepKey] = 'disetujui';
            } else {
                $stepStatus[$stepKey] = 'menunggu';
            }
        }

        $approvedSteps = collect($stepStatus)
            ->filter(fn ($value) => $value === 'disetujui')
            ->count();
        $currentStep = min($approvedSteps + 1, $totalSteps);
        $currentStepKey = $stepKeys[$approvedSteps] ?? null;
        $stepTitle = $stepTitles[$currentStepKey] ?? 'Belum Diketahui';

        $hasRevisi = in_array('revisi', $stepStatus, true);
        $currentStatus = match (true) {
            $hasRevisi => 'Perlu Revisi',
            $approvedSteps >= $totalSteps => 'Selesai',
            $approvedSteps > 0 => 'Disetujui',
            default => 'Menunggu',
        };

        return (object)[
            'id' => $notif->id,
            'user_name' => $notif->user->name ?? '-',
            'user_jabatan' => $notif->user->jabatan ?? '-',
            'number' => $notif->number,
            'file' => $notif->file,
            'tanggal' => $notif->created_at->format('d-m-Y H:i'),
            'handled_by' => $notif->assignedAdmin?->name ?? '-',
            'status' => $currentStatus,
            'progress' => round(($approvedSteps / $totalSteps) * 100),
            'current_step' => $currentStep,
            'current_step_title' => $stepTitle,
            'approved_steps' => $approvedSteps,
            'is_completed' => $approvedSteps >= $totalSteps,
        ];
    });

    $notifications->setCollection($requests);

    return view('admin.permintaansikpgo', [
        'requests' => $notifications,
        'notifications' => $notifications,
        'adminList' => \App\Models\User::whereHas('handledNotifications')->pluck('name', 'id'),
        'bulanList' => [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ],
        'tahunList' => range(date('Y'), date('Y') - 5),
        'totalSteps' => $totalSteps,
    ]);
}


}

 
