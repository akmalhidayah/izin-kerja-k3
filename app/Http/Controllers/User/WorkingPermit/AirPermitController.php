<?php

namespace App\Http\Controllers\User\WorkingPermit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkPermitAir;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitClosure;
use App\Models\Notification;
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
'signature_verificator' => 'nullable|string',
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

        if (!$request->boolean('_token_access')) {
            $notification = Notification::where('id', $validated['notification_id'])
                ->where('user_id', auth()->id())
                ->first();

            if (!$notification) {
                return back()->with('error', 'Notifikasi tidak valid.');
            }
        }

        $validated['notification_id'] = (int) $validated['notification_id'];

        $clearAllSignatures = $request->boolean('clear_all_signatures');

        $existing = WorkPermitAir::where('notification_id', $validated['notification_id'])->first();
        $existingDetail = WorkPermitDetail::where('notification_id', $validated['notification_id'])->first();
        $existingClosure = $existingDetail
            ? WorkPermitClosure::where('work_permit_detail_id', $existingDetail->id)->first()
            : null;

        $validated['signature_permit_requestor'] = $this->saveSignature($request->input('signature_permit_requestor'), 'requestor')
            ?? $existing?->signature_permit_requestor;

        $validated['signature_verificator'] = $this->saveSignature($request->input('signature_verificator'), 'verificator')
            ?? $existing?->signature_verificator;

        $validated['signature_permit_issuer'] = $this->saveSignature($request->input('signature_permit_issuer'), 'issuer')
            ?? $existing?->signature_permit_issuer;

        $validated['senior_manager_signature'] = $this->saveSignature($request->input('senior_manager_signature'), 'senior_manager')
            ?? $existing?->senior_manager_signature;

        $validated['general_manager_signature'] = $this->saveSignature($request->input('general_manager_signature'), 'general_manager')
            ?? $existing?->general_manager_signature;

        $validated['signature_permit_authorizer'] = $this->saveSignature($request->input('signature_permit_authorizer'), 'authorizer')
            ?? $existing?->signature_permit_authorizer;

        $validated['signature_permit_receiver'] = $this->saveSignature($request->input('signature_permit_receiver'), 'receiver')
            ?? $existing?->signature_permit_receiver;

        $validated['requestor_signature'] = $this->saveSignature($request->input('requestor_signature'), 'close_requestor')
            ?? ($existingClosure?->requestor_sign ?? null);

        $validated['issuer_signature'] = $this->saveSignature($request->input('issuer_signature'), 'close_issuer')
            ?? ($existingClosure?->issuer_sign ?? null);


        // Sketsa
        if ($request->hasFile('sketsa_pekerjaan')) {
            $path = $request->file('sketsa_pekerjaan')->store('sketsa/air', 'public');
            $validated['sketsa_pekerjaan'] = 'storage/' . $path;
        } else {
            $existing = WorkPermitAir::where('notification_id', $validated['notification_id'])->first();
            $validated['sketsa_pekerjaan'] = $existing?->sketsa_pekerjaan;
        }

$validated['daftar_pekerja'] = json_encode($request->input('daftar_pekerja', []));


        $validated['persyaratan_perairan'] = json_encode($request->input('perairan', []));
        $validated['verified_workers'] = json_encode($request->input('verified_workers', []));

        $validated['rekomendasi_kerja_aman'] = $validated['rekomendasi_tambahan'] ?? null;
        $validated['rekomendasi_kerja_aman_check'] = $validated['rekomendasi_status'] ?? null;

        // Main
$permit = WorkPermitAir::updateOrCreate(
            ['notification_id' => $validated['notification_id']],
            collect($validated)->only([
                'permit_requestor_name',
                'signature_permit_requestor',
                'permit_requestor_date',
                'permit_requestor_time',
                'verified_workers',
                'verificator_name',
'signature_verificator',
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
// Ini baru aman digunakan:
if (!$permit->token) {
    $permit->token = Str::uuid();
    $permit->save();
}

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

        // Closure logic aman
        $closure = WorkPermitClosure::firstOrNew(['work_permit_detail_id' => $detail->id]);
        $closure->lock_tag_removed = $request->filled('lock_tag') ? $request->input('lock_tag') === 'ya' : $closure->lock_tag_removed;
        $closure->equipment_cleaned = $request->filled('sampah_peralatan') ? $request->input('sampah_peralatan') === 'ya' : $closure->equipment_cleaned;
        $closure->guarding_restored = $request->filled('machine_guarding') ? $request->input('machine_guarding') === 'ya' : $closure->guarding_restored;
        $closure->closed_date = $validated['penutupan_tanggal'] ?? $closure->closed_date;
        $closure->closed_time = $validated['penutupan_jam'] ?? $closure->closed_time;
        $closure->requestor_name = $validated['requestor_name'] ?? $closure->requestor_name;
        $closure->requestor_sign = $validated['requestor_signature'] ?? $closure->requestor_sign;
        $closure->issuer_name = $validated['issuer_name'] ?? $closure->issuer_name;
        $closure->issuer_sign = $validated['issuer_signature'] ?? $closure->issuer_sign;
        $closure->save();

        if ($clearAllSignatures) {
            $permit->forceFill([
                'signature_permit_requestor' => null,
                'signature_verificator' => null,
                'signature_permit_issuer' => null,
                'senior_manager_signature' => null,
                'general_manager_signature' => null,
                'signature_permit_authorizer' => null,
                'signature_permit_receiver' => null,
            ])->save();

            $closure->forceFill([
                'requestor_sign' => null,
                'issuer_sign' => null,
            ])->save();
        }

        return back()->with('success', 'Data Work Permit Air berhasil disimpan!');
    }
public function storeByToken(Request $request, $token)
{
    $permit = WorkPermitAir::where('token', $token)->firstOrFail();
    $request->merge(['notification_id' => $permit->notification_id]);
    $request->merge(['_token_access' => true]);

    app()->call([$this, 'store'], ['request' => $request]);

    session()->flash('alert', 'Data berhasil disimpan melalui link token!');
    return back();
}

    public function showByToken($token)
{
    $permit = WorkPermitAir::where('token', $token)->firstOrFail();
    $notification = $permit->notification;
    $detail = $permit->detail;
    $closure = $permit->closure;

    return view('pengajuan-user.workingpermit.form-token-air', [
        'permit' => $permit,
        'notification' => $notification,
        'detail' => $detail,
        'closure' => $closure,
        'jenis' => 'air',
    ]);
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
