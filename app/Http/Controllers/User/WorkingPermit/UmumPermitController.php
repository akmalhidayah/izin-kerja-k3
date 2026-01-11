<?php

namespace App\Http\Controllers\User\WorkingPermit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\UmumWorkPermit;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitClosure;
use App\Models\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UmumPermitController extends Controller
{


public function store(Request $request)
{
    try {
        $validated = Validator::make($request->all(), [
            'notification_id' => 'required|exists:notifications,id',
            'izin_khusus' => 'nullable|array',
            'isolasi_listrik' => 'nullable|array',
            'isolasi_non_listrik' => 'nullable|array',
            'checklist_kerja_aman' => 'nullable|array',
            'rekomendasi_tambahan' => 'nullable|string',
            'rekomendasi_status' => 'nullable|string',

            'permit_requestor_name' => 'nullable|string',
            'permit_requestor_sign' => 'nullable|string',
            'permit_requestor_date' => 'nullable|date',
            'permit_requestor_time' => 'nullable',

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

            'live_testing_items' => 'nullable|array',
            'live_testing_name' => 'nullable|string',
            'live_testing_sign' => 'nullable|string',
            'live_testing_date' => 'nullable|date',
            'live_testing_time' => 'nullable',

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

    $isDraft = $request->input('action') === 'save';
    $clearAllSignatures = $request->boolean('clear_all_signatures');

    $validated['permit_requestor_sign'] = $this->saveSignature($request->input('signature_permit_requestor'), 'requestor');
    $validated['permit_issuer_sign'] = $this->saveSignature($request->input('signature_permit_issuer'), 'issuer');
    $validated['permit_authorizer_sign'] = $this->saveSignature($request->input('signature_permit_authorizer'), 'authorizer');
    $validated['permit_receiver_sign'] = $this->saveSignature($request->input('signature_permit_receiver'), 'receiver');
    $validated['live_testing_sign'] = $this->saveSignature($request->input('signature_live_testing'), 'testing');

    $validated['izin_khusus'] = $request->input('izin_khusus', []);
    $validated['isolasi_listrik'] = json_encode($request->input('isolasi_listrik', []));
    $validated['isolasi_non_listrik'] = json_encode($request->input('isolasi_non_listrik', []));
    $validated['checklist_kerja_aman'] = json_encode($request->input('checklist_kerja_aman', []));
    $validated['live_testing_items'] = json_encode($request->input('live_testing', []));

    $permit = UmumWorkPermit::updateOrCreate(
        ['notification_id' => $validated['notification_id']],
        array_filter([
            'izin_khusus' => json_encode($validated['izin_khusus']),
            'isolasi_listrik' => $validated['isolasi_listrik'],
            'isolasi_non_listrik' => $validated['isolasi_non_listrik'],
            'checklist_kerja_aman' => $validated['checklist_kerja_aman'],
            'rekomendasi_tambahan' => $validated['rekomendasi_tambahan'],
            'rekomendasi_status' => $validated['rekomendasi_status'] ?? null,
            'permit_requestor_name' => $request->input('permit_requestor_name'),
            'permit_requestor_sign' => $validated['permit_requestor_sign'],
            'permit_requestor_date' => $request->input('permit_requestor_date'),
            'permit_requestor_time' => $request->input('permit_requestor_time'),
            'permit_issuer_name' => $request->input('permit_issuer_name'),
            'permit_issuer_sign' => $validated['permit_issuer_sign'],
            'permit_issuer_date' => $request->input('permit_issuer_date'),
            'permit_issuer_time' => $request->input('permit_issuer_time'),
            'izin_berlaku_dari' => $request->input('izin_berlaku_dari'),
            'izin_berlaku_jam_dari' => $request->input('izin_berlaku_jam_dari'),
            'izin_berlaku_sampai' => $request->input('izin_berlaku_sampai'),
            'izin_berlaku_jam_sampai' => $request->input('izin_berlaku_jam_sampai'),
            'permit_authorizer_name' => $request->input('permit_authorizer_name'),
            'permit_authorizer_sign' => $validated['permit_authorizer_sign'],
            'permit_authorizer_date' => $request->input('permit_authorizer_date'),
            'permit_authorizer_time' => $request->input('permit_authorizer_time'),
            'permit_receiver_name' => $request->input('permit_receiver_name'),
            'permit_receiver_sign' => $validated['permit_receiver_sign'],
            'permit_receiver_date' => $request->input('permit_receiver_date'),
            'permit_receiver_time' => $request->input('permit_receiver_time'),
            'live_testing_items' => $validated['live_testing_items'],
            'live_testing_name' => $request->input('live_testing_name'),
            'live_testing_sign' => $validated['live_testing_sign'],
            'live_testing_date' => $request->input('live_testing_date'),
            'live_testing_time' => $request->input('live_testing_time'),
        ], fn ($v) => $v !== null && $v !== '')
    );

    $detail = WorkPermitDetail::updateOrCreate(
        ['notification_id' => $validated['notification_id']],
        array_filter([
            'permit_type' => 'umum',
            'location' => $request->input('lokasi_pekerjaan'),
            'work_date' => $request->input('tanggal_pekerjaan'),
            'job_description' => $request->input('uraian_pekerjaan'),
            'equipment' => $request->input('peralatan_digunakan'),
            'worker_count' => is_numeric($request->input('jumlah_pekerja')) ? (int) $request->input('jumlah_pekerja') : null,
            'emergency_contact' => $request->input('nomor_darurat'),
        ], fn ($v) => $v !== null && $v !== '')
    );

    $closeRequestorSign = $this->saveSignature($request->input('signature_close_requestor'), 'close_requestor');
    $closeIssuerSign = $this->saveSignature($request->input('signature_close_issuer'), 'close_issuer');

    if ($request->boolean('clear_all_signatures')) {
        $validated['permit_requestor_sign'] = null;
        $validated['permit_issuer_sign'] = null;
        $validated['permit_authorizer_sign'] = null;
        $validated['permit_receiver_sign'] = null;
        $validated['live_testing_sign'] = null;
        $closeRequestorSign = null;
        $closeIssuerSign = null;
    }

    $closure = WorkPermitClosure::updateOrCreate(
        ['work_permit_detail_id' => $detail->id],
        array_filter([
            'lock_tag_removed' => $request->input('close_lock_tag') === 'ya',
            'equipment_cleaned' => $request->input('close_tools') === 'ya',
            'guarding_restored' => $request->input('close_guarding') === 'ya',
            'closed_date' => $request->input('close_date'),
            'closed_time' => $request->input('close_time'),
            'requestor_name' => $request->input('close_requestor_name'),
            'requestor_sign' => $closeRequestorSign,
            'issuer_name' => $request->input('close_issuer_name'),
            'issuer_sign' => $closeIssuerSign,
            'jumlah_rfid' => $request->input('jumlah_rfid'),
        ], fn ($v) => $v !== null && $v !== '')
    );

    if ($clearAllSignatures) {
        $permit->forceFill([
            'permit_requestor_sign' => null,
            'permit_issuer_sign' => null,
            'permit_authorizer_sign' => null,
            'permit_receiver_sign' => null,
            'live_testing_sign' => null,
        ])->save();

        if ($closure) {
            $closure->forceFill([
                'requestor_sign' => null,
                'issuer_sign' => null,
            ])->save();
        }
    }
if (!$permit->token) {
    $permit->token = Str::uuid();
    $permit->save();
}

    return back()->with('success', 'Data Working Permit Umum berhasil disimpan!')->withInput();
}

    private function saveSignature($base64, $role)
    {
        if (!$base64 || !str_starts_with($base64, 'data:image')) return null;

        $folder = 'signatures/working-permit/umum/';
        $filename = $role . '_' . Str::random(10) . '.png';
        $path = storage_path('app/public/' . $folder);

        if (!file_exists($path)) mkdir($path, 0777, true);

        $image = str_replace('data:image/png;base64,', '', $base64);
        $image = str_replace(' ', '+', $image);
        file_put_contents($path . $filename, base64_decode($image));

        return 'storage/' . $folder . $filename;
    }
 public function showByToken($token)
{
    $permit = UmumWorkPermit::where('token', $token)->firstOrFail();
    $notification = $permit->notification;

    // Fix: Ambil dari relasi langsung ke permit
    $detail = $permit->detail;
    $closure = $permit->closure;

    $isTokenForm = true;

    return view('pengajuan-user.workingpermit.formpermitumum', compact('permit', 'notification', 'detail', 'closure', 'isTokenForm'));
}


    public function storeByToken(Request $request, $token)
    {
        $permit = UmumWorkPermit::where('token', $token)->firstOrFail();

        try {
            $validated = $request->validate([
                'izin_khusus' => 'nullable|array',
                'isolasi_listrik' => 'nullable|array',
                'isolasi_non_listrik' => 'nullable|array',
                'checklist_kerja_aman' => 'nullable|array',
                'rekomendasi_tambahan' => 'nullable|string',
                'rekomendasi_status' => 'nullable|string',

                'permit_requestor_name' => 'nullable|string',
                'permit_requestor_sign' => 'nullable|string',
                'permit_requestor_date' => 'nullable|date',
                'permit_requestor_time' => 'nullable',

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

                'live_testing_items' => 'nullable|array',
                'live_testing_name' => 'nullable|string',
                'live_testing_sign' => 'nullable|string',
                'live_testing_date' => 'nullable|date',
                'live_testing_time' => 'nullable',
                
            ]);
            
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        // Handle signature uploads
        $clearAllSignatures = $request->boolean('clear_all_signatures');
$validated['permit_requestor_sign'] = $this->saveSignature($request->input('signature_permit_requestor'), 'requestor');
$validated['permit_issuer_sign'] = $this->saveSignature($request->input('signature_permit_issuer'), 'issuer');
$validated['permit_authorizer_sign'] = $this->saveSignature($request->input('signature_permit_authorizer'), 'authorizer');
$validated['permit_receiver_sign'] = $this->saveSignature($request->input('signature_permit_receiver'), 'receiver');
$validated['live_testing_sign'] = $this->saveSignature($request->input('signature_live_testing'), 'testing');


        // JSON fields
        $validated['izin_khusus'] = $request->input('izin_khusus', []);
        $validated['isolasi_listrik'] = json_encode($request->input('isolasi_listrik', []));
        $validated['isolasi_non_listrik'] = json_encode($request->input('isolasi_non_listrik', []));
        $validated['checklist_kerja_aman'] = json_encode($request->input('checklist_kerja_aman', []));
$validated['live_testing_items'] = json_encode($request->input('live_testing', []));


        $permit->update(array_filter($validated, fn ($v) => $v !== null && $v !== ''));
 // ✅ Tambahkan bagian ini SETELAH update permit:
    $detail = WorkPermitDetail::updateOrCreate(
        ['notification_id' => $permit->notification_id],
        array_filter([
            'permit_type' => 'umum',
            'location' => $request->input('lokasi_pekerjaan'),
            'work_date' => $request->input('tanggal_pekerjaan'),
            'job_description' => $request->input('uraian_pekerjaan'),
            'equipment' => $request->input('peralatan_digunakan'),
            'worker_count' => is_numeric($request->input('jumlah_pekerja')) ? (int) $request->input('jumlah_pekerja') : null,
            'emergency_contact' => $request->input('nomor_darurat'),
        ], fn ($v) => $v !== null && $v !== '')
    );

    $closure = WorkPermitClosure::updateOrCreate(
        ['work_permit_detail_id' => $detail->id],
        array_filter([
            'lock_tag_removed' => $request->input('close_lock_tag') === 'ya',
            'equipment_cleaned' => $request->input('close_tools') === 'ya',
            'guarding_restored' => $request->input('close_guarding') === 'ya',
            'closed_date' => $request->input('close_date'),
            'closed_time' => $request->input('close_time'),
            'requestor_name' => $request->input('close_requestor_name'),
            'requestor_sign' => $this->saveSignature($request->input('signature_close_requestor'), 'close_requestor'),
            'issuer_name' => $request->input('close_issuer_name'),
            'issuer_sign' => $this->saveSignature($request->input('signature_close_issuer'), 'close_issuer'),
                                'jumlah_rfid' => $request->input('jumlah_rfid'),
 
        ], fn ($v) => $v !== null && $v !== '')
    );

        if ($clearAllSignatures) {
            $permit->forceFill([
                'permit_requestor_sign' => null,
                'permit_issuer_sign' => null,
                'permit_authorizer_sign' => null,
                'permit_receiver_sign' => null,
                'live_testing_sign' => null,
            ])->save();

            if ($closure) {
                $closure->forceFill([
                    'requestor_sign' => null,
                    'issuer_sign' => null,
                ])->save();
            }
        }
        return back()->with('success', 'Form Working Permit Umum berhasil diperbarui.');
    }

 public function preview($id)
{
    $permit = UmumWorkPermit::where('notification_id', $id)->first();
    $detail = WorkPermitDetail::where('notification_id', $id)->first();

    if (!$permit && !$detail) {
        abort(404, 'Data izin kerja umum tidak ditemukan.');
    }

    $closure = $detail
        ? WorkPermitClosure::where('work_permit_detail_id', $detail->id)->first()
        : null;

    // ✅ Tambahan: konversi izin_khusus menjadi array
    $permit->izin_khusus = is_array($permit->izin_khusus)
        ? $permit->izin_khusus
        : json_decode($permit->izin_khusus ?? '[]', true);

    if (!is_array($permit->izin_khusus)) {
        $permit->izin_khusus = []; // fallback hardening
    }

    return Pdf::loadView('pengajuan-user.workingpermit.umumpdf', compact('permit', 'detail', 'closure'))
        ->setPaper('A4')
        ->stream('izin-kerja-umum.pdf');
}

}
