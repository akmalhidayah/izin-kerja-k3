<?php

namespace App\Http\Controllers\User\WorkingPermit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkPermitRuangTertutup;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitClosure;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RuangTertutupPermitController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'notification_id' => 'required|integer|exists:notifications,id',

                // Bagian 1
                'lokasi_pekerjaan' => 'nullable|string',
                'tanggal_pekerjaan' => 'nullable|date',
                'uraian_pekerjaan' => 'nullable|string',
                'peralatan_digunakan' => 'nullable|string',
                'jumlah_pekerja' => 'nullable|integer',
                'nomor_darurat' => 'nullable|string',

                // Bagian 2-12 (JSON)
                'isolasi_listrik' => 'nullable|array',
                'isolasi_non_listrik' => 'nullable|array',
                'pengukuran_gas' => 'nullable|array',
                'syarat_ruang_tertutup' => 'nullable|array',
                'rekomendasi_tambahan' => 'nullable|string',
                'rekomendasi_status' => 'nullable|string',

                // Bagian 6
                'permit_requestor_name' => 'nullable|string',
                'signature_permit_requestor' => 'nullable|string',
                'permit_requestor_date' => 'nullable|date',
                'permit_requestor_time' => 'nullable',

                // Bagian 7
                'confined_verificator_name' => 'nullable|string',
                'signature_confined_verificator' => 'nullable|string',
                'confined_verificator_date' => 'nullable|date',
                'confined_verificator_time' => 'nullable',

                // Bagian 8
                'permit_issuer_name' => 'nullable|string',
                'signature_permit_issuer' => 'nullable|string',
                'permit_issuer_date' => 'nullable|date',
                'permit_issuer_time' => 'nullable',
                'izin_berlaku_dari' => 'nullable|date',
                'izin_berlaku_jam_dari' => 'nullable',
                'izin_berlaku_sampai' => 'nullable|date',
                'izin_berlaku_jam_sampai' => 'nullable',

                // Bagian 9
                'permit_authorizer_name' => 'nullable|string',
                'signature_permit_authorizer' => 'nullable|string',
                'permit_authorizer_date' => 'nullable|date',
                'permit_authorizer_time' => 'nullable',

                // Bagian 10
                'permit_receiver_name' => 'nullable|string',
                'signature_permit_receiver' => 'nullable|string',
                'permit_receiver_date' => 'nullable|date',
                'permit_receiver_time' => 'nullable',

                // Bagian 11
                'pekerja_masuk_keluar' => 'nullable|array',

                // Bagian 12
                'live_testing_checklist' => 'nullable|array',
                'live_testing_name' => 'nullable|string',
                'live_testing_signature' => 'nullable|string',
                'live_testing_date' => 'nullable|date',
                'live_testing_time' => 'nullable',

                // Bagian 13 (Closure)
                'close_lock_tag' => 'nullable|string',
                'close_tools' => 'nullable|string',
                'close_guarding' => 'nullable|string',
                'close_date' => 'nullable|date',
                'close_time' => 'nullable',
                'signature_close_requestor' => 'nullable|string',
                'signature_close_issuer' => 'nullable|string',
            ])->validate();
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $notification_id = (int) $validated['notification_id'];

        // Encode array fields
        $jsonFields = [
            'isolasi_listrik', 'isolasi_non_listrik', 'pengukuran_gas',
            'syarat_ruang_tertutup', 'pekerja_masuk_keluar', 'live_testing_checklist'
        ];
        foreach ($jsonFields as $field) {
            $validated[$field] = json_encode($request->input($field, []));
        }

        // Simpan tanda tangan (Bagian 6-10)
$existing = WorkPermitRuangTertutup::where('notification_id', $notification_id)->first();

$validated['signature_permit_requestor'] = $this->saveSignature($request->input('signature_permit_requestor'), 'requestor')
    ?? $existing?->signature_permit_requestor;

$validated['signature_confined_verificator'] = $this->saveSignature($request->input('signature_confined_verificator'), 'verificator')
    ?? $existing?->signature_confined_verificator;

$validated['signature_permit_issuer'] = $this->saveSignature($request->input('signature_permit_issuer'), 'issuer')
    ?? $existing?->signature_permit_issuer;

$validated['signature_permit_authorizer'] = $this->saveSignature($request->input('signature_permit_authorizer'), 'authorizer')
    ?? $existing?->signature_permit_authorizer;

$validated['signature_permit_receiver'] = $this->saveSignature($request->input('signature_permit_receiver'), 'receiver')
    ?? $existing?->signature_permit_receiver;
$validated['live_testing_signature'] = $this->saveSignature($request->input('live_testing_signature'), 'livetesting')
    ?? $existing?->live_testing_signature;

        // Simpan ke tabel utama
        $ruangTertutup = WorkPermitRuangTertutup::updateOrCreate(
            ['notification_id' => $notification_id],
            array_merge($validated, ['notification_id' => $notification_id])
        );
        if (!$ruangTertutup->token) {
    $ruangTertutup->token = Str::uuid();
    $ruangTertutup->save();
}


        // Simpan ke tabel detail (Bagian 1)
        $detail = WorkPermitDetail::updateOrCreate(
            ['notification_id' => $notification_id],
            array_filter([
                'permit_type' => 'ruangtertutup',
                'location' => $validated['lokasi_pekerjaan'] ?? null,
                'work_date' => $validated['tanggal_pekerjaan'] ?? null,
                'job_description' => $validated['uraian_pekerjaan'] ?? null,
                'equipment' => $validated['peralatan_digunakan'] ?? null,
                'worker_count' => $validated['jumlah_pekerja'] ?? null,
                'emergency_contact' => $validated['nomor_darurat'] ?? null,
            ], fn($v) => $v !== null && $v !== '')
        );

        // Simpan ke tabel closure (Bagian 13)
        WorkPermitClosure::updateOrCreate(
            ['work_permit_detail_id' => $detail->id],
            array_filter([
                'lock_tag_removed' => $request->input('close_lock_tag') === 'ya',
                'equipment_cleaned' => $request->input('close_tools') === 'ya',
                'guarding_restored' => $request->input('close_guarding') === 'ya',
                'closed_date' => $validated['close_date'] ?? null,
                'closed_time' => $validated['close_time'] ?? null,
                'requestor_sign' => $this->saveSignature($request->input('signature_close_requestor'), 'close_requestor'),
                'issuer_sign' => $this->saveSignature($request->input('signature_close_issuer'), 'close_issuer'),
            ], fn($v) => $v !== null && $v !== '')
        );

        return back()->with('success', 'Data Izin Kerja Ruang Tertutup berhasil disimpan!');
    }
    public function showByToken($token)
{
    $permit = WorkPermitRuangTertutup::where('token', $token)->firstOrFail();
    $notification = $permit->notification;
    $detail = $permit->detail;
    $closure = $permit->closure;

    return view('pengajuan-user.workingpermit.form-token-ruang-tertutup', [
        'permit' => $permit,
        'notification' => $notification,
        'detail' => $detail,
        'closure' => $closure,
        'jenis' => 'ruangtertutup',
    ]);
}
public function storeByToken(Request $request, $token)
{
    $permit = WorkPermitRuangTertutup::where('token', $token)->firstOrFail();
    $request->merge(['notification_id' => $permit->notification_id]);

    app()->call([$this, 'store'], ['request' => $request]);
    session()->flash('alert', 'Data berhasil disimpan melalui link token!');
    
    return back();
}


    private function saveSignature($base64, $role)
    {
        if (!$base64 || !str_starts_with($base64, 'data:image')) return null;

        $folder = 'signatures/working-permit/ruangtertutup/';
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
        $permit = WorkPermitRuangTertutup::where('notification_id', $id)->first();
        $detail = WorkPermitDetail::where('notification_id', $id)->first();
        $closure = $detail
            ? WorkPermitClosure::where('work_permit_detail_id', $detail->id)->first()
            : null;

        if (!$permit && !$detail) {
            abort(404, 'Data izin kerja ruang tertutup tidak ditemukan.');
        }

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('pengajuan-user.workingpermit.ruangtertutuppdf', compact('permit', 'detail', 'closure'))
            ->setPaper('A4')
            ->stream('izin-kerja-ruang-tertutup.pdf');
    }
}
