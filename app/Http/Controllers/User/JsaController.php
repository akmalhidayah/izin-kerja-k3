<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Jsa;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class JsaController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'notification_id' => 'required|exists:notifications,id',
                'nama_perusahaan' => 'nullable|string',
                'no_jsa' => 'nullable|string',
                'nama_jsa' => 'nullable|string',
                'departemen' => 'nullable|string',
                'area_kerja' => 'nullable|string',
                'tanggal' => 'nullable|date',
                'dibuat_nama' => 'nullable|string',
                'dibuat_signature' => 'nullable|string',
                'disetujui_nama' => 'nullable|string',
                'disetujui_signature' => 'nullable|string',
                'diverifikasi_nama' => 'nullable|string',
                'diverifikasi_signature' => 'nullable|string',
                'langkah_kerja' => 'nullable|string',
            ])->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        // Signature Handling
        $validated['dibuat_signature'] = $this->saveSignature($request->input('dibuat_signature'), 'dibuat');
        $validated['disetujui_signature'] = $this->saveSignature($request->input('disetujui_signature'), 'disetujui');
        $validated['diverifikasi_signature'] = $this->saveSignature($request->input('diverifikasi_signature'), 'diverifikasi');

        // Langkah Kerja JSON
        $validated['langkah_kerja'] = $request->input('langkah_kerja') ?: '[]';

        // Nomor JSA otomatis
        $bulanTahun = now()->format('mY');
        $prefix = "JSA/ST/{$bulanTahun}";
        $lastJsa = Jsa::where('no_jsa', 'like', "%$prefix")->orderBy('created_at', 'desc')->first();
        $nextNumber = 1;
        if ($lastJsa) {
            $lastNo = (int)substr($lastJsa->no_jsa, 0, 3);
            $nextNumber = $lastNo + 1;
        }
        $validated['no_jsa'] = str_pad($nextNumber, 3, '0', STR_PAD_LEFT) . "/$prefix";

        // Simpan Data
        $jsa = Jsa::create($validated);

        // Generate token jika belum ada
        if (!$jsa->token) {
            $jsa->token = Str::uuid();
            $jsa->save();
        }

        return back()->with('success', 'Data JSA berhasil disimpan!');
    }

    public function edit($id)
    {
        $jsa = Jsa::findOrFail($id);
        $jsa->langkah_kerja = is_string($jsa->langkah_kerja) ? json_decode($jsa->langkah_kerja) : $jsa->langkah_kerja;
        return view('pengajuan-user.jsa.edit', compact('jsa'));
    }

    public function update(Request $request, $id)
    {
        $validated = Validator::make($request->all(), [
            'nama_perusahaan' => 'nullable|string',
            'no_jsa' => 'nullable|string',
            'nama_jsa' => 'nullable|string',
            'departemen' => 'nullable|string',
            'area_kerja' => 'nullable|string',
            'tanggal' => 'nullable|date',
            'dibuat_nama' => 'required|string',
            'dibuat_signature' => 'nullable|string',
            'disetujui_nama' => 'required|string',
            'disetujui_signature' => 'nullable|string',
            'diverifikasi_nama' => 'required|string',
            'diverifikasi_signature' => 'nullable|string',
            'langkah_kerja' => 'nullable|string',
        ])->validate();

        $jsa = Jsa::findOrFail($id);

        // Signature Handling
        $validated['dibuat_signature'] = $this->saveSignature($request->input('dibuat_signature'), 'dibuat') ?: $jsa->dibuat_signature;
        $validated['disetujui_signature'] = $this->saveSignature($request->input('disetujui_signature'), 'disetujui') ?: $jsa->disetujui_signature;
        $validated['diverifikasi_signature'] = $this->saveSignature($request->input('diverifikasi_signature'), 'diverifikasi') ?: $jsa->diverifikasi_signature;

        $validated['langkah_kerja'] = $request->input('langkah_kerja') ?: '[]';

        $jsa->update($validated);

        return back()->with('success', 'Data JSA berhasil diperbarui!');
    }

    public function showByToken($token)
    {
        $jsa = Jsa::where('token', $token)->firstOrFail();
        return view('pengajuan-user.jsa.form', compact('jsa'));
    }

    public function storeByToken(Request $request, $token)
    {
        $jsa = Jsa::where('token', $token)->firstOrFail();

        $validated = Validator::make($request->all(), [
            'nama_perusahaan' => 'nullable|string',
            'nama_jsa' => 'nullable|string',
            'departemen' => 'nullable|string',
            'area_kerja' => 'nullable|string',
            'tanggal' => 'nullable|date',
            'dibuat_nama' => 'nullable|string',
            'dibuat_signature' => 'nullable|string',
            'disetujui_nama' => 'nullable|string',
            'disetujui_signature' => 'nullable|string',
            'diverifikasi_nama' => 'nullable|string',
            'diverifikasi_signature' => 'nullable|string',
            'langkah_kerja' => 'nullable|string',
        ])->validate();

        $validated['dibuat_signature'] = $this->saveSignature($request->input('dibuat_signature'), 'dibuat') ?: $jsa->dibuat_signature;
        $validated['disetujui_signature'] = $this->saveSignature($request->input('disetujui_signature'), 'disetujui') ?: $jsa->disetujui_signature;
        $validated['diverifikasi_signature'] = $this->saveSignature($request->input('diverifikasi_signature'), 'diverifikasi') ?: $jsa->diverifikasi_signature;

        $validated['langkah_kerja'] = $request->input('langkah_kerja') ?: '[]';

        $jsa->update($validated);

        return back()->with('success', 'Data JSA berhasil disimpan.');
    }

    public function showPdf($notification_id)
    {
        $jsa = Jsa::where('notification_id', $notification_id)->firstOrFail();
        $jsa->langkah_kerja = json_decode($jsa->langkah_kerja, true);
        $pdf = Pdf::loadView('pengajuan-user.jsa.pdfjsa', compact('jsa'));
        $filename = 'jsa_' . str_replace(['/', '\\'], '_', $jsa->no_jsa) . '.pdf';
        return $pdf->stream($filename);
    }

    private function saveSignature($base64, $role)
    {
        if (!$base64 || !str_starts_with($base64, 'data:image')) return null;

        $folder = 'signatures/jsa/';
        $filename = $role . '_' . Str::random(10) . '.png';
        $path = storage_path('app/public/' . $folder);

        if (!file_exists($path)) mkdir($path, 0777, true);

        $image = str_replace('data:image/png;base64,', '', $base64);
        $image = str_replace(' ', '+', $image);
        file_put_contents($path . $filename, base64_decode($image));

        return 'storage/' . $folder . $filename;
    }

    public function getGeneratedNoJsa()
    {
        $bulanTahun = now()->format('mY');
        $prefix = "JSA/ST/{$bulanTahun}";
        $lastJsa = Jsa::where('no_jsa', 'like', "%$prefix")->orderBy('created_at', 'desc')->first();
        $nextNumber = 1;
        if ($lastJsa) {
            $lastNo = (int)substr($lastJsa->no_jsa, 0, 3);
            $nextNumber = $lastNo + 1;
        }
        return str_pad($nextNumber, 3, '0', STR_PAD_LEFT) . "/$prefix";
    }
}
