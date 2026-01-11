<?php

namespace App\Http\Controllers\User\WorkingPermit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkPermitPenggalian;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitClosure;
use App\Models\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;

class PenggalianPermitController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'notification_id' => 'required|integer|exists:notifications,id',

                'denah' => 'nullable|array',
                'denah_status' => 'nullable|string',
                'file_denah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',

                'syarat_penggalian' => 'nullable|array',
                'rekomendasi_tambahan' => 'nullable|string',
                'rekomendasi_status' => 'nullable|string',

                'permit_requestor_name' => 'nullable|string',
                'signature_permit_requestor' => 'nullable|string',
                'permit_requestor_date' => 'nullable|date',
                'permit_requestor_time' => 'nullable',

                'verificator_name' => 'nullable|string',
                'signature_verificator' => 'nullable|string',
                'verificator_date' => 'nullable|date',
                'verificator_time' => 'nullable',

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

                // Detail
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

        if (!$request->boolean('_token_access')) {
            $notification = Notification::where('id', $validated['notification_id'])
                ->where('user_id', auth()->id())
                ->first();

            if (!$notification) {
                return back()->with('error', 'Notifikasi tidak valid.');
            }
        }
        $clearAllSignatures = $request->boolean('clear_all_signatures');

        $notification_id = (int) $validated['notification_id'];
        $validated['denah'] = json_encode($request->input('denah', []));
        $validated['syarat_penggalian'] = json_encode($request->input('syarat_penggalian', []));

        if ($request->hasFile('file_denah')) {
            $path = $request->file('file_denah')->store('uploads/denah', 'public');
$validated['file_denah'] = $path;

        }

        $validated['signature_permit_requestor'] = $this->saveSignature($request->input('signature_permit_requestor'), 'requestor');
        $validated['signature_verificator'] = $this->saveSignature($request->input('signature_verificator'), 'verificator');
        $validated['signature_permit_issuer'] = $this->saveSignature($request->input('signature_permit_issuer'), 'issuer');
        $validated['signature_permit_authorizer'] = $this->saveSignature($request->input('signature_permit_authorizer'), 'authorizer');
        $validated['signature_permit_receiver'] = $this->saveSignature($request->input('signature_permit_receiver'), 'receiver');

        $penggalianData = array_filter([
            'notification_id' => $notification_id,
            'denah' => $validated['denah'],
            'denah_status' => $validated['denah_status'] ?? null,
            'file_denah' => $validated['file_denah'] ?? null,
            'syarat_penggalian' => $validated['syarat_penggalian'],
            'rekomendasi_tambahan' => $validated['rekomendasi_tambahan'] ?? null,
            'rekomendasi_status' => $validated['rekomendasi_status'] ?? null,
            'permit_requestor_name' => $validated['permit_requestor_name'] ?? null,
            'signature_permit_requestor' => $validated['signature_permit_requestor'],
            'permit_requestor_date' => $validated['permit_requestor_date'] ?? null,
            'permit_requestor_time' => $validated['permit_requestor_time'] ?? null,
            'verificator_name' => $validated['verificator_name'] ?? null,
            'signature_verificator' => $validated['signature_verificator'],
            'verificator_date' => $validated['verificator_date'] ?? null,
            'verificator_time' => $validated['verificator_time'] ?? null,
            'permit_issuer_name' => $validated['permit_issuer_name'] ?? null,
            'signature_permit_issuer' => $validated['signature_permit_issuer'],
            'permit_issuer_date' => $validated['permit_issuer_date'] ?? null,
            'permit_issuer_time' => $validated['permit_issuer_time'] ?? null,
            'izin_berlaku_dari' => $validated['izin_berlaku_dari'] ?? null,
            'izin_berlaku_jam_dari' => $validated['izin_berlaku_jam_dari'] ?? null,
            'izin_berlaku_sampai' => $validated['izin_berlaku_sampai'] ?? null,
            'izin_berlaku_jam_sampai' => $validated['izin_berlaku_jam_sampai'] ?? null,
            'permit_authorizer_name' => $validated['permit_authorizer_name'] ?? null,
            'signature_permit_authorizer' => $validated['signature_permit_authorizer'],
            'permit_authorizer_date' => $validated['permit_authorizer_date'] ?? null,
            'permit_authorizer_time' => $validated['permit_authorizer_time'] ?? null,
            'permit_receiver_name' => $validated['permit_receiver_name'] ?? null,
            'signature_permit_receiver' => $validated['signature_permit_receiver'],
            'permit_receiver_date' => $validated['permit_receiver_date'] ?? null,
            'permit_receiver_time' => $validated['permit_receiver_time'] ?? null,
        ], fn($v) => $v !== null && $v !== '');

        $permit = WorkPermitPenggalian::updateOrCreate([
            'notification_id' => $notification_id
        ], $penggalianData);

        if ($clearAllSignatures) {
            $permit->forceFill([
                'signature_permit_requestor' => null,
                'signature_verificator' => null,
                'signature_permit_issuer' => null,
                'signature_permit_authorizer' => null,
                'signature_permit_receiver' => null,
            ])->save();
        }

        if (!$permit->token) {
            $permit->token = Str::uuid();
            $permit->save();
        }

        $detail = WorkPermitDetail::updateOrCreate(
            ['notification_id' => $notification_id],
            array_filter([
                'permit_type' => 'penggalian',
                'location' => $validated['lokasi_pekerjaan'] ?? null,
                'work_date' => $validated['tanggal_pekerjaan'] ?? null,
                'job_description' => $validated['uraian_pekerjaan'] ?? null,
                'equipment' => $validated['peralatan_digunakan'] ?? null,
                'worker_count' => $validated['jumlah_pekerja'] ?? null,
                'emergency_contact' => $validated['nomor_darurat'] ?? null,
            ], fn($v) => $v !== null && $v !== '')
        );

        return back()->with('success', 'Data Izin Kerja Penggalian berhasil disimpan!');
    }

    public function showByToken($token)
    {
        $permit = WorkPermitPenggalian::with(['detail', 'closure', 'notification'])->where('token', $token)->firstOrFail();

        return view('pengajuan-user.workingpermit.form-token-penggalian', [
            'permit' => $permit,
            'notification' => $permit->notification,
            'detail' => $permit->detail,
            'closure' => $permit->closure,
            'jenis' => 'penggalian'
        ]);
    }

    public function storeByToken(Request $request, $token)
    {
        $permit = WorkPermitPenggalian::where('token', $token)->firstOrFail();
        $request->merge(['notification_id' => $permit->notification_id]);
        $request->merge(['_token_access' => true]);
        app()->call([$this, 'store'], ['request' => $request]);
        session()->flash('alert', 'Data berhasil disimpan melalui link token!');
        return back();
    }

    private function saveSignature($base64, $role)
    {
        if (!$base64 || !str_starts_with($base64, 'data:image')) return null;

        $folder = 'signatures/working-permit/penggalian/';
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
        $permit = WorkPermitPenggalian::where('notification_id', $id)->first();
        $detail = WorkPermitDetail::where('notification_id', $id)->first();
        $closure = $detail
            ? WorkPermitClosure::where('work_permit_detail_id', $detail->id)->first()
            : null;

        if (!$permit && !$detail) {
            abort(404, 'Data izin kerja penggalian tidak ditemukan.');
        }

        return Pdf::loadView('pengajuan-user.workingpermit.penggalianpdf', compact('permit', 'detail', 'closure'))
            ->setPaper('A4')
            ->stream('izin-kerja-penggalian.pdf');
    }
}
