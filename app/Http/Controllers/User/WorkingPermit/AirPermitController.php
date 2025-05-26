<?php

namespace App\Http\Controllers\User\WorkingPermit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkPermitAir;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitClosure;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class AirPermitController extends Controller
{
public function store(Request $request)
{
    $validated = Validator::make($request->all(), [
        'notification_id' => 'required|integer|exists:notifications,id',

        // Bagian 1
        'lokasi_pekerjaan' => 'nullable|string',
        'tanggal_pekerjaan' => 'nullable|date',
        'uraian_pekerjaan' => 'nullable|string',
        'peralatan_digunakan' => 'nullable|string',
        'jumlah_pekerja' => 'nullable|integer',
        'nomor_darurat' => 'nullable|string',

        // Bagian 2
        'nama_pekerja' => 'nullable|array',
        'paraf_pekerja' => 'nullable|array',
        'sketsa_pekerjaan' => 'nullable|file|mimes:jpg,jpeg,png,pdf',

        // Bagian 3
        'perairan' => 'nullable|array',

        // Bagian 4
        'rekomendasi_tambahan' => 'nullable|string',
        'rekomendasi_status' => 'nullable|string',

        // Bagian 5-9
        'permit_requestor_name' => 'nullable|string',
        'signature_permit_requestor' => 'nullable|string',
        'permit_requestor_date' => 'nullable|date',
        'permit_requestor_time' => 'nullable',

        'verified_workers' => 'nullable|array',
        'verificator_name' => 'nullable|string',
        'verificator_sign' => 'nullable|string',
        'verificator_date' => 'nullable|date',
        'verificator_time' => 'nullable',

        'permit_issuer_name' => 'nullable|string',
        'signature_permit_issuer' => 'nullable|string',
        'senior_manager_name' => 'nullable|string',
        'senior_manager_signature' => 'nullable|string',
        'general_manager_name' => 'nullable|string',
        'general_manager_signature' => 'nullable|string',
        'izin_berlaku_dari' => 'nullable|date',
        'izin_berlaku_jam_dari' => 'nullable',
        'izin_berlaku_sampai' => 'nullable|date',
        'izin_berlaku_jam_sampai' => 'nullable',

        'permit_authorizer_name' => 'nullable|string',
        'signature_permit_authorizer' => 'nullable|string',
        'permit_authorizer_date' => 'nullable|date',
        'permit_authorizer_time' => 'nullable',

        'permit_receiver_name' => 'nullable|string',
        'signature_permit_receiver' => 'nullable|string',
        'permit_receiver_date' => 'nullable|date',
        'permit_receiver_time' => 'nullable',

        // Bagian 10 (Closure)
        'lock_tag' => 'nullable|string',
        'sampah_peralatan' => 'nullable|string',
        'machine_guarding' => 'nullable|string',
        'penutupan_tanggal' => 'nullable|date',
        'penutupan_jam' => 'nullable',
        'requestor_name' => 'nullable|string',
        'requestor_signature' => 'nullable|string',
        'issuer_name' => 'nullable|string',
        'issuer_signature' => 'nullable|string',
    ])->validate();

    $validated['notification_id'] = (int) $validated['notification_id'];

    // Save signatures
    $validated['signature_permit_requestor'] = $this->saveSignature($request->input('signature_permit_requestor'), 'requestor');
    $validated['verificator_sign'] = $this->saveSignature($request->input('verificator_sign'), 'verificator');
    $validated['signature_permit_issuer'] = $this->saveSignature($request->input('signature_permit_issuer'), 'issuer');
    $validated['senior_manager_signature'] = $this->saveSignature($request->input('senior_manager_signature'), 'senior_manager');
    $validated['general_manager_signature'] = $this->saveSignature($request->input('general_manager_signature'), 'general_manager');
    $validated['signature_permit_authorizer'] = $this->saveSignature($request->input('signature_permit_authorizer'), 'authorizer');
    $validated['signature_permit_receiver'] = $this->saveSignature($request->input('signature_permit_receiver'), 'receiver');
    $validated['requestor_signature'] = $this->saveSignature($request->input('requestor_signature'), 'close_requestor');
    $validated['issuer_signature'] = $this->saveSignature($request->input('issuer_signature'), 'close_issuer');

    // Upload sketsa
    if ($request->hasFile('sketsa_pekerjaan')) {
        $path = $request->file('sketsa_pekerjaan')->store('sketsa/air', 'public');
        $validated['sketsa_pekerjaan'] = 'storage/' . $path;
    }

    // JSON encode array fields
    $validated['daftar_pekerja'] = json_encode(collect($request->input('nama_pekerja', []))->map(function ($nama, $i) use ($request) {
        return [
            'nama' => $nama,
            'paraf' => $request->input('paraf_pekerja')[$i] ?? null,
        ];
    })->values());

    $validated['persyaratan_perairan'] = json_encode($request->input('perairan', []));
    $validated['verified_workers'] = json_encode($request->input('verified_workers', []));

    // Mapping rekomendasi fields
    $validated['rekomendasi_kerja_aman'] = $validated['rekomendasi_tambahan'] ?? null;
    $validated['rekomendasi_kerja_aman_check'] = $validated['rekomendasi_status'] ?? null;

    // Save to WorkPermitAir
    WorkPermitAir::updateOrCreate(
        ['notification_id' => $validated['notification_id']],
        collect($validated)->only([
            'permit_requestor_name',
            'signature_permit_requestor',
            'permit_requestor_date',
            'permit_requestor_time',
            'verified_workers',
            'verificator_name',
            'verificator_sign',
            'verificator_date',
            'verificator_time',
            'permit_issuer_name',
            'signature_permit_issuer',
            'senior_manager_name',
            'senior_manager_signature',
            'general_manager_name',
            'general_manager_signature',
            'izin_berlaku_dari',
            'izin_berlaku_jam_dari',
            'izin_berlaku_sampai',
            'izin_berlaku_jam_sampai',
            'permit_authorizer_name',
            'signature_permit_authorizer',
            'permit_authorizer_date',
            'permit_authorizer_time',
            'permit_receiver_name',
            'signature_permit_receiver',
            'permit_receiver_date',
            'permit_receiver_time',
            'rekomendasi_kerja_aman',
            'rekomendasi_kerja_aman_check',
            'daftar_pekerja',
            'persyaratan_perairan',
            'sketsa_pekerjaan',
        ])->toArray()
    );

    // Save to WorkPermitDetail
    $detail = WorkPermitDetail::updateOrCreate(
        ['notification_id' => $validated['notification_id']],
        [
            'permit_type' => 'air',
            'location' => $validated['lokasi_pekerjaan'] ?? null,
            'work_date' => $validated['tanggal_pekerjaan'] ?? null,
            'job_description' => $validated['uraian_pekerjaan'] ?? null,
            'equipment' => $validated['peralatan_digunakan'] ?? null,
            'worker_count' => $validated['jumlah_pekerja'] ?? null,
            'emergency_contact' => $validated['nomor_darurat'] ?? null,
        ]
    );

    // Save to WorkPermitClosure
    WorkPermitClosure::updateOrCreate(
        ['work_permit_detail_id' => $detail->id],
        [
            'lock_tag_removed' => $request->input('lock_tag') === 'ya',
            'equipment_cleaned' => $request->input('sampah_peralatan') === 'ya',
            'guarding_restored' => $request->input('machine_guarding') === 'ya',
            'closed_date' => $validated['penutupan_tanggal'] ?? null,
            'closed_time' => $validated['penutupan_jam'] ?? null,
            'requestor_name' => $validated['requestor_name'] ?? null,
            'requestor_sign' => $validated['requestor_signature'],
            'issuer_name' => $validated['issuer_name'] ?? null,
            'issuer_sign' => $validated['issuer_signature'],
        ]
    );

    return back()->with('success', 'Data Work Permit Air berhasil disimpan!');
}

    private function saveSignature($base64, $role)
    {
        if (!$base64 || !str_starts_with($base64, 'data:image')) return null;
        $folder = 'signatures/working-permit/air/';
        $filename = $role . '_' . Str::random(10) . '.png';
        $path = storage_path('app/public/' . $folder);
        if (!file_exists($path)) mkdir($path, 0777, true);
        file_put_contents($path . $filename, base64_decode(str_replace('data:image/png;base64,', '', str_replace(' ', '+', $base64))));
        return 'storage/' . $folder . $filename;
    }

    public function preview($id)
    {
        $permit = WorkPermitAir::where('notification_id', $id)->first();
        $detail = WorkPermitDetail::where('notification_id', $id)->first();
        $closure = $detail ? WorkPermitClosure::where('work_permit_detail_id', $detail->id)->first() : null;

        if (!$permit && !$detail) abort(404, 'Data izin kerja air tidak ditemukan.');

        return Pdf::loadView('pengajuan-user.workingpermit.airpdf', compact('permit', 'detail', 'closure'))
            ->setPaper('A4')
            ->stream('izin-kerja-air.pdf');
    }
}
