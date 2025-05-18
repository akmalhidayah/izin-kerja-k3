<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Jsa;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitClosure;
use App\Models\StepApproval;

class IzinKerjaController extends Controller
{
public function index()
{
    $notification = Notification::where('user_id', auth()->id())->latest()->first();
    $jsa = $notification ? Jsa::where('notification_id', $notification->id)->first() : null;
    $permit = $notification?->umumWorkPermit;

    $detail = $notification
        ? WorkPermitDetail::where('notification_id', $notification->id)->first()
        : null;

    $closure = $detail
        ? WorkPermitClosure::where('work_permit_detail_id', $detail->id)->first()
        : null;

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

    $stepApprovals = $notification 
        ? StepApproval::where('notification_id', $notification->id)->pluck('status', 'step')->toArray()
        : [];

    $steps = [];
    foreach ($stepTitles as $code => $title) {
        $raw = strtolower($stepApprovals[$code] ?? 'menunggu');
        $steps[] = [
            'title' => $title,
            'code' => $code,
            'status' => match($raw) {
                'disetujui' => 'done',
                'revisi' => 'revisi',
                default => 'pending',
            }
        ];
    }

    return view('pengajuan-user.izin-kerja', compact(
        'notification', 'jsa', 'permit', 'detail', 'closure', 'steps'
    ));
}

}