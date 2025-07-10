<?php

namespace App\Http\Controllers\User\WorkingPermit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkPermitBeban;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitClosure;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;


class BebanPermitController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'notification_id' => 'required|integer|exists:notifications,id',

// ✅ Bagian VALIDATOR: Hanya izinkan nilai '1'
'dok_operator' => 'nullable|in:1',
'dok_rigger' => 'nullable|in:1',
'dok_sertifikat' => 'nullable|in:1',
'dok_loadchart' => 'nullable|in:1',
'dok_rencana_pengangkatan' => 'nullable|in:1',
'dok_jsa' => 'nullable|in:1',



                // Bagian 3
                'persyaratan_kerja_aman' => 'nullable|array',

                // Bagian 4
                'rekomendasi_kerja_aman' => 'nullable|string',
                'rekomendasi_status' => 'nullable|string',

                // Bagian 5–9
                'permit_requestor_name' => 'nullable|string',
'signature_permit_requestor' => 'nullable|string',
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

                // Bagian 10
                'close_lock_tag' => 'nullable|string',
                'close_tools' => 'nullable|string',
                'close_guarding' => 'nullable|string',
                'close_date' => 'nullable|date',
                'close_time' => 'nullable',
                'close_requestor_name' => 'nullable|string',
                'signature_close_requestor' => 'nullable|string',
                'close_issuer_name' => 'nullable|string',
                'signature_close_issuer' => 'nullable|string',

                // Detail pekerjaan
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
foreach (['dok_operator', 'dok_rigger', 'dok_sertifikat', 'dok_loadchart', 'dok_rencana_pengangkatan', 'dok_jsa'] as $item) {
    $validated[$item] = (bool) $request->input($item); // ✔ konversi ke true/false
}
\Log::debug('SIGNATURE INPUT:', [
    'permit_requestor_sign' => $request->input('permit_requestor_sign')
]);


// Ambil permit yang sudah ada (kalau sedang edit)
$existing = WorkPermitBeban::where('notification_id', $validated['notification_id'])->first();

$validated['signature_permit_requestor'] = $this->saveSignature(
    $request->input('signature_permit_requestor'),
    'requestor'
) ?? $existing?->signature_permit_requestor;

$validated['signature_verificator'] = $this->saveSignature(
    $request->input('signature_verificator'),
    'verificator'
) ?? $existing?->signature_verificator;

$validated['signature_permit_issuer'] = $this->saveSignature(
    $request->input('signature_permit_issuer'),
    'issuer'
) ?? $existing?->signature_permit_issuer;

$validated['signature_permit_authorizer'] = $this->saveSignature(
    $request->input('signature_permit_authorizer'),
    'authorizer'
) ?? $existing?->signature_permit_authorizer;

$validated['signature_permit_receiver'] = $this->saveSignature(
    $request->input('signature_permit_receiver'),
    'receiver'
) ?? $existing?->signature_permit_receiver;

        $validated['persyaratan_kerja_aman'] = json_encode($request->input('persyaratan_kerja_aman', []));

        $permit = WorkPermitBeban::updateOrCreate(
            ['notification_id' => $validated['notification_id']],
            array_merge(
                $validated,
                ['token' => Str::uuid()]
            )
        );

        // Simpan detail
        $detail = WorkPermitDetail::updateOrCreate(
            ['notification_id' => $validated['notification_id']],
            array_filter([
                'permit_type' => 'beban',
                'location' => $validated['lokasi_pekerjaan'] ?? null,
                'work_date' => $validated['tanggal_pekerjaan'] ?? null,
                'job_description' => $validated['uraian_pekerjaan'] ?? null,
                'equipment' => $validated['peralatan_digunakan'] ?? null,
                'worker_count' => $validated['jumlah_pekerja'] ?? null,
                'emergency_contact' => $validated['nomor_darurat'] ?? null,
            ], fn($v) => $v !== null && $v !== '')
        );

        // Simpan closure
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

        return back()->with('success', 'Data izin kerja pengangkatan beban berhasil disimpan!');
    }

    public function showByToken($token)
{
    $permit = WorkPermitBeban::with(['detail', 'closure', 'notification'])->where('token', $token)->firstOrFail();


    $notification = $permit->notification;
    $detail = $permit->detail;
    $closure = $permit->closure;

    return view('pengajuan-user.workingpermit.form-token-beban', [
         'permit' => $permit,
       'permitBeban' => $permit,
        'notification' => $notification,
        'detail' => $detail,
        'closure' => $closure,
        'jenis' => 'beban',
    ]);
}


public function storeByToken(Request $request, $token)
{
    $permit = WorkPermitBeban::where('token', $token)->firstOrFail();
    $request->merge(['notification_id' => $permit->notification_id]);

    // Reuse logic penyimpanan utama
    app()->call([$this, 'store'], ['request' => $request]);

    session()->flash('alert', 'Data berhasil disimpan melalui link token!');
    return back();
}

    private function saveSignature($base64, $role)
    {
        if (!$base64 || !str_starts_with($base64, 'data:image')) return null;

        $folder = 'signatures/working-permit/pengangkatan/';
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
    $permit = WorkPermitBeban::where('notification_id', $id)->first();
    $detail = WorkPermitDetail::where('notification_id', $id)->first();
    $closure = $detail
        ? WorkPermitClosure::where('work_permit_detail_id', $detail->id)->first()
        : null;

    if (!$permit && !$detail) {
        abort(404, 'Data izin kerja pengangkatan beban tidak ditemukan.');
    }

    // Tambahkan ini:
    if (is_string($permit?->persyaratan_kerja_aman)) {
        $permit->persyaratan_kerja_aman = json_decode($permit->persyaratan_kerja_aman, true);
    }

    return Pdf::loadView('pengajuan-user.workingpermit.bebanpdf', compact('permit', 'detail', 'closure'))
        ->setPaper('A4')
        ->stream('izin-kerja-beban.pdf');
}

}
