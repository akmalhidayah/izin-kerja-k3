<?php

namespace App\Http\Controllers\User\WorkingPermit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkPermitGasPanas;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitClosure;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GasPanasPermitController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'notification_id' => 'required|integer|exists:notifications,id',

                'daftar_pekerja' => 'nullable|array',
                'sketsa_pekerjaan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'syarat' => 'nullable|array',
                'verified_workers' => 'nullable|array',

                'rekomendasi_tambahan' => 'nullable|string',
                'rekomendasi_status' => 'nullable|string',

                'permit_requestor_name' => 'nullable|string',
                'permit_requestor_sign' => 'nullable|string',
                'permit_requestor_date' => 'nullable|date',
                'permit_requestor_time' => 'nullable',

                'verificator_name' => 'nullable|string',
                'verificator_sign' => 'nullable|string',
                'verificator_date' => 'nullable|date',
                'verificator_time' => 'nullable',

                'permit_issuer_name' => 'nullable|string',
                'permit_issuer_sign' => 'nullable|string',
                'permit_issuer_date' => 'nullable|date',
                'permit_issuer_time' => 'nullable',
                'izin_berlaku_dari' => 'nullable|date',
                'izin_berlaku_jam_dari' => 'nullable',
                'izin_berlaku_sampai' => 'nullable|date',
                'izin_berlaku_jam_sampai' => 'nullable',

                'permit_authorizer_name' => 'nullable|string',
                'permit_authorizer_sign' => 'nullable|string',
                'permit_authorizer_date' => 'nullable|date',
                'permit_authorizer_time' => 'nullable',

                'permit_receiver_name' => 'nullable|string',
                'permit_receiver_sign' => 'nullable|string',
                'permit_receiver_date' => 'nullable|date',
                'permit_receiver_time' => 'nullable',

                // Penutupan
                'close_lock_tag' => 'nullable|string',
                'close_tools' => 'nullable|string',
                'close_guarding' => 'nullable|string',
                'close_date' => 'nullable|date',
                'close_time' => 'nullable',
                'close_requestor_name' => 'nullable|string',
                'signature_close_requestor' => 'nullable|string',
                'close_issuer_name' => 'nullable|string',
                'signature_close_issuer' => 'nullable|string',

                // Detail Pekerjaan
                'lokasi_pekerjaan' => 'nullable|string',
                'tanggal_pekerjaan' => 'nullable|date',
                'uraian_pekerjaan' => 'nullable|string',
                'peralatan_digunakan' => 'nullable|string',
                'jumlah_pekerja' => 'nullable|integer',
                'nomor_darurat' => 'nullable|string',
            ])->validate();
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $validated['notification_id'] = (int) $validated['notification_id'];

        // Simpan tanda tangan
$validated['permit_requestor_sign'] = $this->saveSignature($request->input('permit_requestor_sign'), 'requestor');
$validated['verificator_sign'] = $this->saveSignature($request->input('signature_verificator'), 'verificator');
$validated['permit_issuer_sign'] = $this->saveSignature($request->input('signature_permit_issuer'), 'issuer');
$validated['permit_authorizer_sign'] = $this->saveSignature($request->input('signature_permit_authorizer'), 'authorizer');
$validated['permit_receiver_sign'] = $this->saveSignature($request->input('signature_permit_receiver'), 'receiver');

        // Encode array ke JSON
        $validated['daftar_pekerja'] = json_encode($request->input('daftar_pekerja', []));
        $validated['checklist_kerja_aman'] = json_encode($request->input('syarat', []));
$validated['verified_workers'] = json_encode($request->input('verified_workers', []));


// Upload Sketsa
if ($request->hasFile('sketsa_pekerjaan')) {
    $path = $request->file('sketsa_pekerjaan')->store('uploads/sketsa', 'public');
    $validated['sketsa_pekerjaan'] = 'storage/' . $path;
} else {
    $existing = WorkPermitGasPanas::where('notification_id', $validated['notification_id'])->first();
    $validated['sketsa_pekerjaan'] = $existing?->sketsa_pekerjaan;
}
        // Data untuk tabel gas panas SAJA
        $gaspanasData = array_filter([
            'notification_id' => $validated['notification_id'],
            'daftar_pekerja' => $validated['daftar_pekerja'],
            'checklist_kerja_aman' => $validated['checklist_kerja_aman'],
              'sketsa_pekerjaan' => $validated['sketsa_pekerjaan'],
            'rekomendasi_tambahan' => $validated['rekomendasi_tambahan'] ?? null,
            'rekomendasi_status' => $validated['rekomendasi_status'] ?? null,
            'permit_requestor_name' => $validated['permit_requestor_name'] ?? null,
            'permit_requestor_sign' => $validated['permit_requestor_sign'],
            'permit_requestor_date' => $validated['permit_requestor_date'] ?? null,
            'permit_requestor_time' => $validated['permit_requestor_time'] ?? null,
            'verified_workers' => $validated['verified_workers'],
            'verificator_name' => $validated['verificator_name'] ?? null,
            'verificator_sign' => $validated['verificator_sign'],
            'verificator_date' => $validated['verificator_date'] ?? null,
            'verificator_time' => $validated['verificator_time'] ?? null,
            'permit_issuer_name' => $validated['permit_issuer_name'] ?? null,
            'permit_issuer_sign' => $validated['permit_issuer_sign'],
            'permit_issuer_date' => $validated['permit_issuer_date'] ?? null,
            'permit_issuer_time' => $validated['permit_issuer_time'] ?? null,
            'izin_berlaku_dari' => $validated['izin_berlaku_dari'] ?? null,
            'izin_berlaku_jam_dari' => $validated['izin_berlaku_jam_dari'] ?? null,
            'izin_berlaku_sampai' => $validated['izin_berlaku_sampai'] ?? null,
            'izin_berlaku_jam_sampai' => $validated['izin_berlaku_jam_sampai'] ?? null,
            'permit_authorizer_name' => $validated['permit_authorizer_name'] ?? null,
            'permit_authorizer_sign' => $validated['permit_authorizer_sign'],
            'permit_authorizer_date' => $validated['permit_authorizer_date'] ?? null,
            'permit_authorizer_time' => $validated['permit_authorizer_time'] ?? null,
            'permit_receiver_name' => $validated['permit_receiver_name'] ?? null,
            'permit_receiver_sign' => $validated['permit_receiver_sign'],
            'permit_receiver_date' => $validated['permit_receiver_date'] ?? null,
            'permit_receiver_time' => $validated['permit_receiver_time'] ?? null,
        ], fn($v) => $v !== null && $v !== '');


        $permit = WorkPermitGasPanas::updateOrCreate(
    ['notification_id' => $validated['notification_id']],
    $gaspanasData
);

if (!$permit->token) {
    $permit->token = Str::uuid();
    $permit->save();
}


        // Simpan ke tabel detail
        $detail = WorkPermitDetail::updateOrCreate(
            ['notification_id' => $validated['notification_id']],
            array_filter([
                'permit_type' => 'gaspanas',
                'location' => $validated['lokasi_pekerjaan'] ?? null,
                'work_date' => $validated['tanggal_pekerjaan'] ?? null,
                'job_description' => $validated['uraian_pekerjaan'] ?? null,
                'equipment' => $validated['peralatan_digunakan'] ?? null,
                'worker_count' => $validated['jumlah_pekerja'] ?? null,
                'emergency_contact' => $validated['nomor_darurat'] ?? null,
            ], fn($v) => $v !== null && $v !== '')
        );

        // Simpan ke tabel closure
        WorkPermitClosure::updateOrCreate(
            ['work_permit_detail_id' => $detail->id],
            array_filter([
                'lock_tag_removed' => $request->input('close_lock_tag') === 'ya',
                'equipment_cleaned' => $request->input('close_tools') === 'ya',
                'guarding_restored' => $request->input('close_guarding') === 'ya',
                'closed_date' => $validated['close_date'] ?? null,
                'closed_time' => $validated['close_time'] ?? null,
                'requestor_name' => $validated['close_requestor_name'] ?? null,
                'requestor_sign' => $this->saveSignature($request->input('signature_close_requestor'), 'close_requestor'),
                'issuer_name' => $validated['close_issuer_name'] ?? null,
                'issuer_sign' => $this->saveSignature($request->input('signature_close_issuer'), 'close_issuer'),
            ], fn($v) => $v !== null && $v !== '')
        );

        return back()->with('success', 'Data Working Permit Gas Panas berhasil disimpan!');
    }
  public function showByToken($token)
{
$permit = WorkPermitGasPanas::with(['detail', 'closure', 'notification'])->where('token', $token)->firstOrFail();

    $notification = $permit->notification;
    $detail = $permit->detail;
    $closure = $permit->closure;

    return view('pengajuan-user.workingpermit.form-token-gaspanas', [ // ganti ke 1 blade umum
        'permit' => $permit,
        'notification' => $notification,
        'detail' => $detail,
        'closure' => $closure,
        'jenis' => 'gaspanas', // agar bisa dipakai conditional include
    ]);
}

public function storeByToken(Request $request, $token)
{
    $permit = WorkPermitGasPanas::where('token', $token)->firstOrFail();
    $request->merge(['notification_id' => $permit->notification_id]);

    // Simpan data
    app()->call([$this, 'store'], ['request' => $request]);

    // Flash alert JS
    session()->flash('alert', 'Data berhasil disimpan melalui link token!');

    return back();
}

    private function saveSignature($base64, $role)
    {
        if (!$base64 || !str_starts_with($base64, 'data:image')) return null;

        $folder = 'signatures/working-permit/gaspanas/';
        $filename = $role . '_' . Str::random(10) . '.png';
        $path = storage_path('app/public/' . $folder);

        if (!file_exists($path)) mkdir($path, 0777, true);

        $image = str_replace('data:image/png;base64,', '', $base64);
        $image = str_replace(' ', '+', $image);
        file_put_contents($path . $filename, base64_decode($image));

        return 'storage/' . $folder . $filename;
    }
public function preview($id)
{
    $permit = \App\Models\WorkPermitGasPanas::where('notification_id', $id)->first();
    $detail = \App\Models\WorkPermitDetail::where('notification_id', $id)->first();
    $closure = $detail
        ? \App\Models\WorkPermitClosure::where('work_permit_detail_id', $detail->id)->first()
        : null;

    if (!$permit && !$detail) {
        abort(404, 'Data izin kerja gas panas tidak ditemukan.');
    }

    return \Barryvdh\DomPDF\Facade\Pdf::loadView('pengajuan-user.workingpermit.gaspanaspdf', compact('permit', 'detail', 'closure'))
        ->setPaper('A4')
        ->stream('izin-kerja-gaspanas.pdf');
}

}
