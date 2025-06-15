<?php

namespace App\Http\Controllers\User\WorkingPermit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkPermitKetinggian;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitClosure;
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
            'nama_pekerja' => 'nullable|array',
            'paraf_pekerja' => 'nullable|array',
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

        $validated['notification_id'] = (int) $validated['notification_id'];

        // ✅ Simpan signature
        $validated['signature_permit_requestor'] = $this->saveSignature($request->signature_permit_requestor, 'requestor');
        $validated['signature_verifikator'] = $this->saveSignature($request->signature_verifikator, 'verifikator');
        $validated['signature_permit_issuer'] = $this->saveSignature($request->signature_permit_issuer, 'issuer');
        $validated['signature_permit_authorizer'] = $this->saveSignature($request->signature_permit_authorizer, 'authorizer');
        $validated['signature_permit_receiver'] = $this->saveSignature($request->signature_permit_receiver, 'receiver');

        // ✅ Upload sketsa
        $sketsaPath = null;
        if ($request->hasFile('sketsa_pekerjaan')) {
            $path = $request->file('sketsa_pekerjaan')->store('sketsa/ketinggian', 'public');
            $sketsaPath = 'storage/' . $path;
        }

        // ✅ JSON pekerja
        $validated['nama_pekerja'] = collect($request->input('nama_pekerja', []))->map(function ($nama, $i) use ($request) {
            return [
                'nama' => $nama,
                'paraf' => $request->input('paraf_pekerja')[$i] ?? null,
            ];
        })->toArray();

        $validated['kerja_aman_ketinggian'] = $request->input('kerja_aman_ketinggian', []);
        $validated['authorized_workers'] = $request->input('authorized_worker', []);

        // ✅ Simpan ke WorkPermitKetinggian
        $permit = WorkPermitKetinggian::updateOrCreate(
            ['notification_id' => $validated['notification_id']],
            array_merge(
                collect($validated)->only([
                    'permit_requestor_name', 'signature_permit_requestor', 'permit_requestor_date', 'permit_requestor_time',
                    'authorized_workers', 'verifikator_name', 'signature_verifikator', 'verifikator_date', 'verifikator_time',
                    'permit_issuer_name', 'signature_permit_issuer', 'permit_issuer_date', 'permit_issuer_time',
                    'izin_berlaku_dari', 'izin_berlaku_jam_dari', 'izin_berlaku_sampai', 'izin_berlaku_jam_sampai',
                    'permit_authorizer_name', 'signature_permit_authorizer', 'permit_authorizer_date', 'permit_authorizer_time',
                    'permit_receiver_name', 'signature_permit_receiver', 'permit_receiver_date', 'permit_receiver_time',
                    'rekomendasi_tambahan', 'rekomendasi_ada',
                ])->toArray(),
                [
                    'nama_pekerja' => $validated['nama_pekerja'],
                    'kerja_aman_ketinggian' => $validated['kerja_aman_ketinggian'],
                    'sketsa_pekerjaan' => $sketsaPath,
                ]
            )
        );

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
        WorkPermitClosure::updateOrCreate(
            ['work_permit_detail_id' => $detail->id],
            [
                'lock_tag_removed' => $request->input('close_lock_tag') === 'ya',
                'equipment_cleaned' => $request->input('close_tools') === 'ya',
                'guarding_restored' => $request->input('close_guarding') === 'ya',
                'closed_date' => $validated['close_date'] ?? null,
                'closed_time' => $validated['close_time'] ?? null,
                'requestor_sign' => $this->saveSignature($request->signature_close_requestor, 'close_requestor'),
                'issuer_sign' => $this->saveSignature($request->signature_close_issuer, 'close_issuer'),
            ]
        );

        return back()->with('success', 'Data Izin Kerja Ketinggian berhasil disimpan!');
    }

    private function saveSignature($base64, $role)
    {
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
    $permit = \App\Models\WorkPermitGasPanas::where('notification_id', $id)->first();
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
