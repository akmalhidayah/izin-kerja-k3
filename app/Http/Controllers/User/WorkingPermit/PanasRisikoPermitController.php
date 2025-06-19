<?php

namespace App\Http\Controllers\User\WorkingPermit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkPermitRisikoPanas;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitClosure;
use Illuminate\Support\Facades\Validator;
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
                'lock_tag' => 'nullable|string',
                'sampah_peralatan' => 'nullable|string',
                'machine_guarding' => 'nullable|string',
                'penutupan_tanggal' => 'nullable|date',
                'penutupan_jam' => 'nullable',
                'requestor_name_close' => 'nullable|string',
                'requestor_signature_close' => 'nullable|string',
                'issuer_name_close' => 'nullable|string',
                'issuer_signature_close' => 'nullable|string',

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

        // Simpan signature
        $validated['signature_requestor'] = $this->saveSignature($request->input('signature_requestor'), 'requestor');
        $validated['signature_verificator'] = $this->saveSignature($request->input('signature_verificator'), 'verificator');
        $validated['signature_permit_issuer'] = $this->saveSignature($request->input('signature_permit_issuer'), 'issuer');
        $validated['signature_senior_manager'] = $this->saveSignature($request->input('signature_senior_manager'), 'senior');
        $validated['signature_general_manager'] = $this->saveSignature($request->input('signature_general_manager'), 'gm');
        $validated['authorizer_signature'] = $this->saveSignature($request->input('authorizer_signature'), 'authorizer');
        $validated['receiver_signature'] = $this->saveSignature($request->input('receiver_signature'), 'receiver');
        $validated['requestor_signature_close'] = $this->saveSignature($request->input('requestor_signature_close'), 'close_requestor');
        $validated['issuer_signature_close'] = $this->saveSignature($request->input('issuer_signature_close'), 'close_issuer');

        // Encode JSON
        $validated['pengukuran_gas'] = json_encode($request->input('pengukuran_gas', []));
        $validated['persyaratan_kerja_panas'] = json_encode($request->input('persyaratan_kerja_panas', []));

        // Simpan ke tabel utama
        WorkPermitRisikoPanas::updateOrCreate(
            ['notification_id' => $validated['notification_id']],
            $validated
        );

        // Simpan ke tabel detail
        $detail = WorkPermitDetail::updateOrCreate(
            ['notification_id' => $validated['notification_id']],
            array_filter([
                'permit_type' => 'risiko-panas',
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
                'lock_tag_removed' => $request->input('lock_tag') === 'ya',
                'equipment_cleaned' => $request->input('sampah_peralatan') === 'ya',
                'guarding_restored' => $request->input('machine_guarding') === 'ya',
                'closed_date' => $validated['penutupan_tanggal'] ?? null,
                'closed_time' => $validated['penutupan_jam'] ?? null,
                'requestor_name' => $validated['requestor_name_close'] ?? null,
                'requestor_sign' => $validated['requestor_signature_close'],
                'issuer_name' => $validated['issuer_name_close'] ?? null,
                'issuer_sign' => $validated['issuer_signature_close'],
            ], fn($v) => $v !== null && $v !== '')
        );

        return back()->with('success', 'Data Risiko Panas berhasil disimpan!');
    }

    private function saveSignature($base64, $role)
    {
        if (!$base64 || !str_starts_with($base64, 'data:image')) return null;

        $folder = 'signatures/working-permit/risiko-panas/';
        $filename = $role . '_' . Str::random(10) . '.png';
        $path = storage_path('app/public/' . $folder);

        if (!file_exists($path)) mkdir($path, 0777, true);

        $image = str_replace('data:image/png;base64,', '', $base64);
        $image = str_replace(' ', '+', $image);
        file_put_contents($path . $filename, base64_decode($image));

        return 'storage/' . $folder . $filename;
    }
}
