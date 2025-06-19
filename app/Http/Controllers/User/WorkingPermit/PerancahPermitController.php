<?php

namespace App\Http\Controllers\User\WorkingPermit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkPermitPerancah;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitClosure;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PerancahPermitController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'notification_id' => 'required|integer|exists:notifications,id',

                // Signature & identitas
                'permit_requestor_name' => 'nullable|string',
                'signature_permit_requestor_perancah' => 'nullable|string',
                'permit_requestor_date' => 'nullable|date',
                'permit_requestor_time' => 'nullable',

                'scaffolding_verificator_name' => 'nullable|string',
                'signature_scaffolding_verificator' => 'nullable|string',
                'scaffolding_verificator_date' => 'nullable|date',
                'scaffolding_verificator_time' => 'nullable',

                'permit_issuer_name' => 'nullable|string',
                'signature_permit_issuer' => 'nullable|string',
                'permit_issuer_date' => 'nullable|date',
                'permit_issuer_time' => 'nullable',
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

                // Persyaratan
                'persyaratan_perancah' => 'nullable|array',
                'persyaratan_keselamatan_perancah' => 'nullable|array',
                'rekomendasi_keselamatan_perancah' => 'nullable|string',
                'rekomendasi_status' => 'nullable|string',

                'scaffolding_verificator_approval' => 'nullable|string',
                'permit_issuer_approval' => 'nullable|string',
                'permit_authorizer_approval' => 'nullable|string',

                // Detail pekerjaan
                'lokasi_pekerjaan' => 'nullable|string',
                'tanggal_pekerjaan' => 'nullable|date',
                'uraian_pekerjaan' => 'nullable|string',
                'peralatan_digunakan' => 'nullable|string',
                'jumlah_pekerja' => 'nullable|integer',
                'nomor_darurat' => 'nullable|string',
                'sketsa_perancah' => 'nullable|string',

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
                'jumlah_rfid' => 'nullable|integer',
            ])->validate();
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        // Simpan signature base64
        $validated['signature_permit_requestor_perancah'] = $this->saveSignature($request->input('signature_permit_requestor_perancah'), 'requestor');
        $validated['signature_scaffolding_verificator'] = $this->saveSignature($request->input('signature_scaffolding_verificator'), 'verificator');
        $validated['signature_permit_issuer'] = $this->saveSignature($request->input('signature_permit_issuer'), 'issuer');
        $validated['signature_permit_authorizer'] = $this->saveSignature($request->input('signature_permit_authorizer'), 'authorizer');
        $validated['signature_permit_receiver'] = $this->saveSignature($request->input('signature_permit_receiver'), 'receiver');

        // Simpan JSON
        $validated['persyaratan_perancah'] = json_encode($request->input('persyaratan_perancah', []));
        $validated['persyaratan_keselamatan_perancah'] = json_encode($request->input('persyaratan_keselamatan_perancah', []));

        // Simpan ke tabel work_permit_perancah
        WorkPermitPerancah::updateOrCreate(
            ['notification_id' => $request->notification_id],
            array_filter($validated, fn($v) => $v !== null && $v !== '')
        );

        // Simpan ke tabel work_permit_details
        $detail = WorkPermitDetail::updateOrCreate(
            ['notification_id' => $request->notification_id],
            array_filter([
                'permit_type' => 'perancah',
                'location' => $request->lokasi_pekerjaan,
                'work_date' => $request->tanggal_pekerjaan,
                'job_description' => $request->uraian_pekerjaan,
                'equipment' => $request->peralatan_digunakan,
                'worker_count' => $request->jumlah_pekerja,
                'emergency_contact' => $request->nomor_darurat,
            ], fn($v) => $v !== null && $v !== '')
        );

        // Simpan ke tabel closure
        WorkPermitClosure::updateOrCreate(
            ['work_permit_detail_id' => $detail->id],
            array_filter([
                'lock_tag_removed' => $request->input('close_lock_tag') === 'ya',
                'equipment_cleaned' => $request->input('close_tools') === 'ya',
                'guarding_restored' => $request->input('close_guarding') === 'ya',
                'closed_date' => $request->close_date,
                'closed_time' => $request->close_time,
                'requestor_name' => $request->close_requestor_name,
                'requestor_sign' => $this->saveSignature($request->signature_close_requestor, 'close_requestor'),
                'issuer_name' => $request->close_issuer_name,
                'issuer_sign' => $this->saveSignature($request->signature_close_issuer, 'close_issuer'),
                'jumlah_rfid' => $request->jumlah_rfid,
            ], fn($v) => $v !== null && $v !== '')
        );

        return back()->with('success', 'Data Working Permit Perancah berhasil disimpan!');
    }

    private function saveSignature($base64, $role)
    {
        if (!$base64 || !str_starts_with($base64, 'data:image')) return null;

        $folder = 'signatures/working-permit/perancah/';
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
        $permit = WorkPermitPerancah::where('notification_id', $id)->first();
        $detail = WorkPermitDetail::where('notification_id', $id)->first();
        $closure = $detail
            ? WorkPermitClosure::where('work_permit_detail_id', $detail->id)->first()
            : null;

        if (!$permit && !$detail) {
            abort(404, 'Data izin kerja perancah tidak ditemukan.');
        }

        return Pdf::loadView('pengajuan-user.workingpermit.perancahpdf', compact('permit', 'detail', 'closure'))
            ->setPaper('A4')
            ->stream('izin-kerja-perancah.pdf');
    }
}
