<?php

namespace App\Http\Controllers\User\WorkingPermit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkPermitRisikoPanas;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitClosure;
use App\Models\Notification;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class PanasRisikoPermitController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'notification_id' => 'required|exists:notifications,id',

                // JSON Fields
                'pengukuran_gas' => 'nullable|array',
                'persyaratan_kerja_panas' => 'nullable|array',

                'rekomendasi_kerja_aman_tambahan' => 'nullable|string',
                'rekomendasi_kerja_aman_setuju' => 'nullable|string',

                // Permohonan
                'requestor_name' => 'nullable|string',
                'signature_requestor' => 'nullable|string',
                'requestor_date' => 'nullable|date',
                'requestor_time' => 'nullable',

                // Verifikasi
                'verificator_name' => 'nullable|string',
                'signature_verificator' => 'nullable|string',
                'verificator_date' => 'nullable|date',
                'verificator_time' => 'nullable',

                // Penerbitan
                'permit_issuer_name' => 'nullable|string',
                'signature_permit_issuer' => 'nullable|string',
                'senior_manager_name' => 'nullable|string',
                'signature_senior_manager' => 'nullable|string',
                'general_manager_name' => 'nullable|string',
                'signature_general_manager' => 'nullable|string',
                'izin_berlaku_dari' => 'nullable|date',
                'izin_berlaku_jam_dari' => 'nullable',
                'izin_berlaku_sampai' => 'nullable|date',
                'izin_berlaku_jam_sampai' => 'nullable',

                // Pengesahan
                'authorizer_name' => 'nullable|string',
                'authorizer_signature' => 'nullable|string',
                'authorizer_date' => 'nullable|date',
                'authorizer_time' => 'nullable',

                // Pelaksanaan
                'receiver_name' => 'nullable|string',
                'receiver_signature' => 'nullable|string',
                'receiver_date' => 'nullable|date',
                'receiver_time' => 'nullable',

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
                'jumlah_rfid' => 'nullable|integer|min:0',

                // Detail
                'lokasi_pekerjaan' => 'nullable|string',
                'tanggal_pekerjaan' => 'nullable|date',
                'uraian_pekerjaan' => 'nullable|string',
                'peralatan_digunakan' => 'nullable|string',
                'jumlah_pekerja' => 'nullable|integer',
                'nomor_darurat' => 'nullable|string',

            ])->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        if (!$request->boolean('_token_access')) {
            $notification = $this->findAccessibleNotification($validated['notification_id']);

            if (!$notification) {
                return back()->with('error', 'Notifikasi tidak valid.');
            }
        }
        $clearAllSignatures = $request->boolean('clear_all_signatures');
        $existing = WorkPermitRisikoPanas::where('notification_id', $validated['notification_id'])->first();
        $existingClosure = $existing?->closure;

        $closeRequestorSign = $this->saveSignature($request->input('signature_close_requestor'), 'close_requestor')
            ?? $existingClosure?->requestor_sign;
        $closeIssuerSign = $this->saveSignature($request->input('signature_close_issuer'), 'close_issuer')
            ?? $existingClosure?->issuer_sign;

        if ($closeRequestorSign) {
            $validated['requestor_signature_close'] = $closeRequestorSign;
        }

        if ($closeIssuerSign) {
            $validated['issuer_signature_close'] = $closeIssuerSign;
        }

        $validated['signature_requestor'] = $this->saveSignature($request->input('signature_requestor'), 'requestor')
            ?? $existing?->signature_requestor;

        $validated['signature_verificator'] = $this->saveSignature($request->input('signature_verificator'), 'verificator')
            ?? $existing?->signature_verificator;

        $validated['signature_permit_issuer'] = $this->saveSignature($request->input('signature_permit_issuer'), 'issuer')
            ?? $existing?->signature_permit_issuer;

        $validated['signature_senior_manager'] = $this->saveSignature($request->input('signature_senior_manager'), 'senior')
            ?? $existing?->signature_senior_manager;

        $validated['signature_general_manager'] = $this->saveSignature($request->input('signature_general_manager'), 'gm')
            ?? $existing?->signature_general_manager;

        $validated['authorizer_signature'] = $this->saveSignature($request->input('authorizer_signature'), 'authorizer')
            ?? $existing?->authorizer_signature;

        $validated['receiver_signature'] = $this->saveSignature($request->input('receiver_signature'), 'receiver')
            ?? $existing?->receiver_signature;
        // Encode JSON
        $validated['pengukuran_gas'] = json_encode($request->input('pengukuran_gas', []));
        $validated['persyaratan_kerja_panas'] = json_encode($request->input('persyaratan_kerja_panas', []));

        // Simpan ke tabel utama
        $permit = WorkPermitRisikoPanas::updateOrCreate(
            ['notification_id' => $validated['notification_id']],
            collect($validated)->only((new WorkPermitRisikoPanas())->getFillable())->toArray()
        );

        $this->ensurePermitToken($permit);

        // Simpan ke tabel detail
        $detail = WorkPermitDetail::updateOrCreate(
            [
                'notification_id' => $validated['notification_id'],
                'permit_type' => 'risiko-panas',
            ],
            array_filter([
                'location' => $validated['lokasi_pekerjaan'] ?? null,
                'work_date' => $validated['tanggal_pekerjaan'] ?? null,
                'job_description' => $validated['uraian_pekerjaan'] ?? null,
                'equipment' => $validated['peralatan_digunakan'] ?? null,
                'worker_count' => $validated['jumlah_pekerja'] ?? null,
                'emergency_contact' => $validated['nomor_darurat'] ?? null,
            ], fn($v) => $v !== null && $v !== '')
        );

        // Simpan ke tabel closure
        $closure = WorkPermitClosure::updateOrCreate(
            ['work_permit_detail_id' => $detail->id],
            array_filter([
                'lock_tag_removed' => $this->radioYesValue($request, 'close_lock_tag'),
                'equipment_cleaned' => $this->radioYesValue($request, 'close_tools'),
                'guarding_restored' => $this->radioYesValue($request, 'close_guarding'),
                'closed_date' => $validated['close_date'] ?? null,
                'closed_time' => $validated['close_time'] ?? null,
                'requestor_name' => $validated['close_requestor_name'] ?? null,
                'requestor_sign' => $closeRequestorSign,
                'issuer_name' => $validated['close_issuer_name'] ?? null,
                'issuer_sign' => $closeIssuerSign,
                'jumlah_rfid' => $validated['jumlah_rfid'] ?? null,
            ], fn($v) => $v !== null && $v !== '')
        );

        if ($clearAllSignatures) {
            $permit->forceFill([
                'signature_requestor' => null,
                'signature_verificator' => null,
                'signature_permit_issuer' => null,
                'signature_senior_manager' => null,
                'signature_general_manager' => null,
                'authorizer_signature' => null,
                'receiver_signature' => null,
                'requestor_signature_close' => null,
                'issuer_signature_close' => null,
            ])->save();

            if ($closure) {
                $closure->forceFill([
                    'requestor_sign' => null,
                    'issuer_sign' => null,
                ])->save();
            }
        }

        return back()->with('success', 'Data Risiko Panas berhasil disimpan!');
    }

    public function showByToken($token)
    {
        $permit = WorkPermitRisikoPanas::where('token', $token)->firstOrFail();
        $this->abortIfPermitTokenExpired($permit);
        $notification = $permit->notification;
        $detail = $permit->detail;
        $closure = $permit->closure;

        return view('pengajuan-user.workingpermit.form-token-risiko-panas', [
            'permit' => $permit,
            'notification' => $notification,
            'detail' => $detail,
            'closure' => $closure,
            'jenis' => 'risiko-panas',
        ]);
    }

    public function storeByToken(Request $request, $token)
    {
        $permit = WorkPermitRisikoPanas::where('token', $token)->firstOrFail();
        $this->abortIfPermitTokenExpired($permit);
        $request->merge(['notification_id' => $permit->notification_id]);
        $request->merge(['_token_access' => true]);

        $response = app()->call([$this, 'store'], ['request' => $request]);

        return $this->tokenStoreResponse($response, 'Data berhasil disimpan melalui link token!', route('token-pdf.show', ['type' => 'risiko-panas', 'token' => $permit->token]));
    }

    private function saveSignature($base64, $role)
    {
        return $this->saveBase64PngSignature($base64, $role, 'signatures/working-permit/risiko-panas/');
    }

    public function preview($id)
    {
        if (!$this->tokenPdfAccessAllowed()) {
            $this->abortUnlessCanAccessNotification($id);
        }

        $permit = \App\Models\WorkPermitRisikoPanas::where('notification_id', $id)->first();
        $detail = $permit?->detail;
        $closure = $permit?->closure;

        if (!$permit && !$detail) {
            abort(404, 'Data izin kerja risiko panas tidak ditemukan.');
        }

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('pengajuan-user.workingpermit.risikopanaspdf', compact('permit', 'detail', 'closure'))
            ->setPaper('A4')
            ->stream('izin-kerja-risiko-panas.pdf');
    }

}
