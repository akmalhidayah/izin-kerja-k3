<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Upload;
use App\Models\Notification;
use App\Models\StepApproval;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminPermintaanController extends Controller
{
    public function show($id)
    {
$notification = Notification::with(['user', 'assignedAdmin'])->findOrFail($id);
// Cek admin yang login vs yang assigned
$loggedInAdmin = auth()->user()->name;
$assignedAdmin = $notification->assignedAdmin?->name;

$showAlert = false;
if ($assignedAdmin && $assignedAdmin !== $loggedInAdmin) {
    $showAlert = true;
}

        
    // Auto-assign admin jika belum ada
    if (!$notification->assigned_admin_id) {
        $notification->assigned_admin_id = auth()->id();
        $notification->save();
    // Tambahkan ini supaya handledBy ke-load
    $notification->refresh();
}
$dataKontraktor = \App\Models\DataKontraktor::where('notification_id', $id)->first();
        $jsa = \App\Models\Jsa::where('notification_id', $id)->first();
$permits = [
    'umum' => \App\Models\UmumWorkPermit::where('notification_id', $id)->first(),
    'gaspanas' => \App\Models\WorkPermitGasPanas::where('notification_id', $id)->first(), 
    'air' => \App\Models\WorkPermitAir::where('notification_id', $id)->first(),
];


        $uploads = \App\Models\Upload::where('notification_id', $id)->get()->keyBy('step');
        $approvals = StepApproval::where('notification_id', $id)->get()->keyBy('step');

        // Inject file_path dari step_approvals kalau upload tidak ada
        foreach ($approvals as $key => $approval) {
            if (!isset($uploads[$key]) && $approval->file_path) {
                $uploads[$key] = (object) [
                    'file_path' => $approval->file_path
                ];
            }
        }

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

        $stepData = [];
        foreach ($stepTitles as $step => $title) {
            $status = $approvals[$step]->status ?? 'Menunggu';
            $upload = $uploads[$step] ?? null;

            if ($step === 'op_spk') {
                $upload = (object)[
                    'file_path' => $notification->file ?? null,
                    'number' => $notification->number ?? null,
                    'type' => $notification->type ?? null,
                    'created_at' => $notification->created_at ?? null,
                ];
            }

            $statusClass = match ($status) {
                'Disetujui' => 'bg-green-500 text-white',
                'Perlu Revisi' => 'bg-yellow-500 text-white',
                'Menunggu' => 'bg-gray-300 text-black',
                default => 'bg-white text-black',
            };

            $stepData[] = [
                'step' => $step,
                'title' => $title,
                'status' => $status,
                'upload' => $upload,
                'status_class' => $statusClass,
                'catatan' => $approvals[$step]->catatan ?? '',
            ];
        }

        // Tambahan: step aktif terakhir
        $lastApprovedIndex = collect($stepData)->where('status', 'Disetujui')->keys()->last();
        $stepSummary = $lastApprovedIndex !== null
            ? 'Step ' . ($lastApprovedIndex + 1) . ' - ' . $stepData[$lastApprovedIndex]['title']
            : 'Belum Diketahui';

        $data = (object)[
            'id' => $notification->id,
            'user_name' => $notification->user->name ?? '-',
            'tanggal' => $notification->created_at->format('d-m-Y H:i'),
'handled_by' => $notification->assignedAdmin?->name ?? '-',
            'status' => $notification->status ?? 'Menunggu',
            'notification_file' => $notification->file ?? null,
            'notification_id' => $notification->id,
            'step_data' => $stepData,
            'step_summary' => $stepSummary, // ✅ properti tambahan
            'data_kontraktor' => $dataKontraktor,
            'jsa' => $jsa,
    'permits' => $permits, // ← tambahkan permits di sini
        ];

        $admins = User::where('usertype', 'admin')->pluck('name');

return view('admin.detailpermintaan', compact('data', 'admins', 'showAlert', 'assignedAdmin'));

    }
public function updateStatus(Request $request, $id, $step)
{
    $request->validate([
        'status' => 'required|in:disetujui,revisi,menunggu',
        'catatan' => 'nullable|string|max:500'
    ]);

    $adminId = auth()->id();
    $notification = \App\Models\Notification::with(['user', 'assignedAdmin'])->findOrFail($id);

    if (!$notification->assigned_admin_id) {
        $notification->update(['assigned_admin_id' => $adminId]);
    } elseif ($notification->assigned_admin_id != $adminId) {
        return back()->with('error', 'Hanya admin yang pertama kali menangani yang dapat mengubah status!');
    }

    $dataUpdate = [
        'status' => strtolower($request->status),
        'catatan' => $request->catatan,
        'approved_by' => $adminId,
    ];

    // 🚀 Tanpa simpan file, generate PDF hanya saat diminta download
    if ($step === 'sik' && strtolower($request->status) === 'disetujui') {
        // Tidak perlu Storage::put(), biarkan PDF hanya di-stream lewat route
    }

    StepApproval::updateOrCreate(
        ['notification_id' => $id, 'step' => $step],
        $dataUpdate
    );

    return redirect()->back()->with('success', 'Status langkah ke-' . $step . ' berhasil diperbarui ke "' . $request->status . '"');
}



    public function deleteFile($id, $step)
{
    // Hapus file dari table uploads
    $upload = Upload::where('notification_id', $id)->where('step', $step)->first();
    if ($upload) {
        Storage::disk('public')->delete($upload->file_path);
        $upload->delete();
    }

    // Hapus path dan status di StepApproval jika ada
    StepApproval::where('notification_id', $id)
        ->where('step', $step)
        ->update([
            'file_path' => null,
            'status' => 'menunggu',
        ]);

    return back()->with('success', 'File berhasil dihapus dan status direset.');
}
public function viewSik($id)
{
    $notification = \App\Models\Notification::with(['user', 'assignedAdmin'])->findOrFail($id);
    $dataKontraktor = \App\Models\DataKontraktor::where('notification_id', $notification->id)->first();

    // Tambahan: ambil tanda tangan SIK
    $sikStep = StepApproval::where('notification_id', $notification->id)
        ->where('step', 'sik')
        ->first();

    $pdf = \PDF::loadView('admin.pdfsik', compact('notification', 'dataKontraktor', 'sikStep'));
    return $pdf->stream('Surat-Izin-Kerja.pdf');
}

}

