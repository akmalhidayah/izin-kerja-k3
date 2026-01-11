<?php

namespace App\Http\Controllers\User\WorkingPermit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkPermitKetinggian;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitClosure;
use App\Models\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class KetinggianPermitController extends Controller
{
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'notification_id' => 'required|integer|exists:notifications,id',

            // Bagian 1 - Detail
            'lokasi_pekerjaan' => 'nullable|string',
            'tanggal_pekerjaan' => 'nullable|date',
            'uraian_pekerjaan' => 'nullable|string',
            'peralatan_digunakan' => 'nullable|string',
            'jumlah_pekerja' => 'nullable|integer',
            'nomor_darurat' => 'nullable|string',

            // Bagian 2
'daftar_pekerja' => 'nullable|array',
'daftar_pekerja.*.nama' => 'nullable|string',
'daftar_pekerja.*.signature' => 'nullable|string',

            'sketsa_pekerjaan' => 'nullable|file|mimes:jpg,jpeg,png,pdf',

            // Bagian 3
            'kerja_aman_ketinggian' => 'nullable|array',

            // Bagian 4
            'rekomendasi_tambahan' => 'nullable|string',
            'rekomendasi_ada' => 'nullable|string',

            // Bagian 5-9
            'permit_requestor_name' => 'nullable|string',
            'signature_permit_requestor' => 'nullable|string',
            'permit_requestor_date' => 'nullable|date',
            'permit_requestor_time' => 'nullable',

            'authorized_worker' => 'nullable|array',
            'verifikator_name' => 'nullable|string',
            'signature_verifikator' => 'nullable|string',
            'verifikator_date' => 'nullable|date',
            'verifikator_time' => 'nullable',

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

            // Penutupan (Bagian 10)
            'close_lock_tag' => 'nullable|string',
            'close_tools' => 'nullable|string',
            'close_guarding' => 'nullable|string',
            'close_date' => 'nullable|date',
            'close_time' => 'nullable',
            'signature_close_requestor' => 'nullable|string',
            'signature_close_issuer' => 'nullable|string',
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

        // ✅ Simpan signature
$existing = WorkPermitKetinggian::where('notification_id', $validated['notification_id'])->first();
$detail = WorkPermitDetail::where('notification_id', $validated['notification_id'])->first();
$closure = $detail
    ? WorkPermitClosure::where('work_permit_detail_id', $detail->id)->first()
    : null;

$validated['signature_permit_requestor'] = $this->saveSignature($request->input('signature_permit_requestor'), 'requestor')
    ?? $existing?->signature_permit_requestor;

$validated['signature_verifikator'] = $this->saveSignature($request->input('signature_verifikator'), 'verifikator')
    ?? $existing?->signature_verifikator;

$signature_permit_issuer = $this->saveSignature($request->input('signature_permit_issuer'), 'issuer')
    ?? $existing?->signature_permit_issuer;
$signature_permit_authorizer = $this->saveSignature($request->input('signature_permit_authorizer'), 'authorizer')
    ?? $existing?->signature_permit_authorizer;
$signature_permit_receiver = $this->saveSignature($request->input('signature_permit_receiver'), 'receiver')
    ?? $existing?->signature_permit_receiver;

$requestor_sign = $request->filled('signature_close_requestor') && $request->input('signature_close_requestor') !== ''
    ? $this->saveSignature($request->input('signature_close_requestor'), 'close_requestor')
    : ($closure?->requestor_sign ?? null);

$issuer_sign = $request->filled('signature_close_issuer') && $request->input('signature_close_issuer') !== ''
    ? $this->saveSignature($request->input('signature_close_issuer'), 'close_issuer')
    : ($closure?->issuer_sign ?? null);



        // ✅ Upload sketsa
$sketsaPath = WorkPermitKetinggian::where('notification_id', $validated['notification_id'])->value('sketsa_pekerjaan');

if ($request->hasFile('sketsa_pekerjaan')) {
    $existing = WorkPermitKetinggian::where('notification_id', $validated['notification_id'])->first();
    if ($existing && $existing->sketsa_pekerjaan) {
        Storage::disk('public')->delete(str_replace('storage/', '', $existing->sketsa_pekerjaan));
    }

    $path = $request->file('sketsa_pekerjaan')->store('sketsa/ketinggian', 'public');
    $sketsaPath = 'storage/' . $path;
}


        // ✅ JSON pekerja
$validated['nama_pekerja'] = collect($request->input('daftar_pekerja', []))
    ->map(fn ($data) => [
        'nama' => $data['nama'] ?? '',
        'signature' => $data['signature'] ?? '',
    ])
    ->toArray();


        $validated['kerja_aman_ketinggian'] = $request->input('kerja_aman_ketinggian', []);
        $validated['authorized_workers'] = $request->input('authorized_worker', []);

       $permit = WorkPermitKetinggian::updateOrCreate(
    ['notification_id' => $validated['notification_id']],
    array_merge(
        collect($validated)->only([
            'permit_requestor_name', 'signature_permit_requestor', 'permit_requestor_date', 'permit_requestor_time',
            'authorized_workers', 'verifikator_name', 'signature_verifikator', 'verifikator_date', 'verifikator_time',
            'permit_issuer_name', 'permit_issuer_date', 'permit_issuer_time',
            'izin_berlaku_dari', 'izin_berlaku_jam_dari', 'izin_berlaku_sampai', 'izin_berlaku_jam_sampai',
            'permit_authorizer_name', 'permit_authorizer_date', 'permit_authorizer_time',
            'permit_receiver_name', 'permit_receiver_date', 'permit_receiver_time',
            'rekomendasi_tambahan', 'rekomendasi_ada',
        ])->toArray(),
        [
            'signature_permit_issuer' => $signature_permit_issuer,
            'signature_permit_authorizer' => $signature_permit_authorizer,
            'signature_permit_receiver' => $signature_permit_receiver,
            'nama_pekerja' => $validated['nama_pekerja'],
            'kerja_aman_ketinggian' => $validated['kerja_aman_ketinggian'],
            'sketsa_pekerjaan' => $sketsaPath,
        ]
    )
);

                if (!$permit->token) {
            $permit->token = Str::uuid();
            $permit->save();
        }

        // ✅ Simpan ke WorkPermitDetail
        $detail = WorkPermitDetail::updateOrCreate(
            ['notification_id' => $validated['notification_id']],
            [
                'permit_type' => 'ketinggian',
                'location' => $validated['lokasi_pekerjaan'] ?? null,
                'work_date' => $validated['tanggal_pekerjaan'] ?? null,
                'job_description' => $validated['uraian_pekerjaan'] ?? null,
                'equipment' => $validated['peralatan_digunakan'] ?? null,
                'worker_count' => $validated['jumlah_pekerja'] ?? null,
                'emergency_contact' => $validated['nomor_darurat'] ?? null,
            ]
        );

        // ✅ Simpan ke WorkPermitClosure
        $closure = WorkPermitClosure::updateOrCreate(
            ['work_permit_detail_id' => $detail->id],
            [
                'lock_tag_removed' => $request->input('close_lock_tag') === 'ya',
                'equipment_cleaned' => $request->input('close_tools') === 'ya',
                'guarding_restored' => $request->input('close_guarding') === 'ya',
                'closed_date' => $validated['close_date'] ?? null,
                'closed_time' => $validated['close_time'] ?? null,
        'requestor_sign' => $requestor_sign,
        'issuer_sign' => $issuer_sign,
            ]
        );

        if ($clearAllSignatures) {
            $permit->forceFill([
                'signature_permit_requestor' => null,
                'signature_verifikator' => null,
                'signature_permit_issuer' => null,
                'signature_permit_authorizer' => null,
                'signature_permit_receiver' => null,
            ])->save();

            if ($closure) {
                $closure->forceFill([
                    'requestor_sign' => null,
                    'issuer_sign' => null,
                ])->save();
            }
        }

        return back()->with('success', 'Data Izin Kerja Ketinggian berhasil disimpan!');
    }
    public function showByToken($token)
{
    $permit = WorkPermitKetinggian::where('token', $token)->firstOrFail();
    $notification = $permit->notification;
    $detail = $permit->detail;
    $closure = $permit->closure;

    return view('pengajuan-user.workingpermit.form-token-ketinggian', [ // sesuaikan blade-nya
        'permit' => $permit,
        'notification' => $notification,
        'detail' => $detail,
        'closure' => $closure,
        'jenis' => 'ketinggian',
    ]);
}

public function storeByToken(Request $request, $token)
{
    $permit = WorkPermitKetinggian::where('token', $token)->firstOrFail();
    $request->merge(['notification_id' => $permit->notification_id]);
    $request->merge(['_token_access' => true]);

    app()->call([$this, 'store'], ['request' => $request]);
    session()->flash('alert', 'Data berhasil disimpan melalui link token!');

    return back();
}


private function saveSignature($base64, $role)
{
    \Log::info("[$role] Signature Input: " . substr($base64, 0, 30)); // Potong biar ga kepanjangan

    if (!$base64 || !str_starts_with($base64, 'data:image')) return null;

    $folder = 'signatures/working-permit/ketinggian/';
    $filename = $role . '_' . Str::random(10) . '.png';
    $path = storage_path('app/public/' . $folder);
    if (!file_exists($path)) mkdir($path, 0777, true);
    file_put_contents($path . $filename, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64)));
    return 'storage/' . $folder . $filename;
}

public function preview($id)
{
    $permit = \App\Models\WorkPermitKetinggian::where('notification_id', $id)->first();
    $detail = \App\Models\WorkPermitDetail::where('notification_id', $id)->first();
    $closure = $detail
        ? \App\Models\WorkPermitClosure::where('work_permit_detail_id', $detail->id)->first()
        : null;

    if (!$permit && !$detail) {
        abort(404, 'Data izin kerja gas panas tidak ditemukan.');
    }

    return \Barryvdh\DomPDF\Facade\Pdf::loadView('pengajuan-user.workingpermit.ketinggianpdf', compact('permit', 'detail', 'closure'))
        ->setPaper('A4')
        ->stream('izin-kerja-ketinggian.pdf');
}
}
