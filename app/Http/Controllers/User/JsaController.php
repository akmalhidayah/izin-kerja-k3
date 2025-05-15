<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Jsa;
use Illuminate\Support\Str;

class JsaController extends Controller
{
public function store(Request $request)
{
    $validated = $request->validate([
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
    ]);

    // ✅ Cek manual kolom yang Wajib (tidak nullable di DB)
    $mandatoryFields = [
        'nama_jsa' => 'Nama JSA wajib diisi.',
        'departemen' => 'Departemen wajib diisi.',
        'area_kerja' => 'Area Kerja wajib diisi.',
        'tanggal' => 'Tanggal wajib diisi.',
        'dibuat_nama' => 'Nama pembuat wajib diisi.',
        'disetujui_nama' => 'Nama penyetuju wajib diisi.',
    ];

    $errors = [];
    foreach ($mandatoryFields as $field => $message) {
        if (empty($validated[$field])) {
            $errors[$field] = $message;
        }
    }

    if (!empty($errors)) {
        return back()->withErrors($errors)->withInput();
    }

    // Simpan tanda tangan ke file
    $validated['dibuat_signature'] = $this->saveSignatureFile($validated['dibuat_signature'], 'dibuat');
    $validated['disetujui_signature'] = $this->saveSignatureFile($validated['disetujui_signature'], 'disetujui');
    $validated['diverifikasi_signature'] = $this->saveSignatureFile($validated['diverifikasi_signature'], 'diverifikasi');

    $validated['langkah_kerja'] = json_encode(json_decode($validated['langkah_kerja'], true) ?? []);

    Jsa::create($validated);

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
        $validated = $request->validate([
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
        ]);

        $jsa = Jsa::findOrFail($id);

        // ✅ cek apakah input signature adalah BASE64 atau path lama
        $validated['dibuat_signature'] = ($request->filled('dibuat_signature') && str_starts_with($validated['dibuat_signature'], 'data:image'))
            ? $this->saveSignatureFile($validated['dibuat_signature'], 'dibuat')
            : $jsa->dibuat_signature;

        $validated['disetujui_signature'] = ($request->filled('disetujui_signature') && str_starts_with($validated['disetujui_signature'], 'data:image'))
            ? $this->saveSignatureFile($validated['disetujui_signature'], 'disetujui')
            : $jsa->disetujui_signature;

        $validated['diverifikasi_signature'] = ($request->filled('diverifikasi_signature') && str_starts_with($validated['diverifikasi_signature'], 'data:image'))
            ? $this->saveSignatureFile($validated['diverifikasi_signature'], 'diverifikasi')
            : $jsa->diverifikasi_signature;

        $validated['langkah_kerja'] = json_encode(json_decode($validated['langkah_kerja'], true) ?? []);

        $jsa->update($validated);

        return back()->with('success', 'Data JSA berhasil diperbarui!');
    }

    public function showPdf($notification_id)
    {
        $jsa = Jsa::where('notification_id', $notification_id)->firstOrFail();
        $jsa->langkah_kerja = json_decode($jsa->langkah_kerja, true);

        $jsa->dibuat_signature = $jsa->dibuat_signature ? public_path($jsa->dibuat_signature) : null;
        $jsa->disetujui_signature = $jsa->disetujui_signature ? public_path($jsa->disetujui_signature) : null;
        $jsa->diverifikasi_signature = $jsa->diverifikasi_signature ? public_path($jsa->diverifikasi_signature) : null;

        $pdf = Pdf::loadView('pengajuan-user.jsa.pdfjsa', compact('jsa'));
        return $pdf->stream('jsa_' . $jsa->no_jsa . '.pdf');
    }

    public function downloadPdf($notification_id)
    {
        $jsa = Jsa::where('notification_id', $notification_id)->firstOrFail();
        $jsa->langkah_kerja = json_decode($jsa->langkah_kerja, true);

        $jsa->dibuat_signature = $jsa->dibuat_signature ? public_path($jsa->dibuat_signature) : null;
        $jsa->disetujui_signature = $jsa->disetujui_signature ? public_path($jsa->disetujui_signature) : null;
        $jsa->diverifikasi_signature = $jsa->diverifikasi_signature ? public_path($jsa->diverifikasi_signature) : null;

        $pdf = Pdf::loadView('pengajuan-user.jsa.pdfjsa', compact('jsa'));
        return $pdf->download('jsa_' . $jsa->no_jsa . '.pdf');
    }

    private function saveSignatureFile($base64, $role, $notificationNumber = null)
    {
        if (!$base64) return null;

        $folder = 'signatures/jsa/';
        if (!file_exists(public_path('storage/' . $folder))) {
            mkdir(public_path('storage/' . $folder), 0777, true);
        }

        $filename = $role . '_' . ($notificationNumber ?? Str::random(10)) . '.png';
        $path = storage_path('app/public/' . $folder . $filename);

        $image = str_replace('data:image/png;base64,', '', $base64);
        $image = str_replace(' ', '+', $image);
        file_put_contents($path, base64_decode($image));

        return 'storage/' . $folder . $filename;
    }
}
