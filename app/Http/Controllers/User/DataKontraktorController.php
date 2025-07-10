<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataKontraktor;
use Illuminate\Support\Facades\Validator;

class DataKontraktorController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'notification_id' => 'required|exists:notifications,id',
                'nama_perusahaan' => 'required|string|max:255',
                'jenis_pekerjaan' => 'nullable|string|max:255',
                'lokasi_kerja'    => 'nullable|string|max:255',
                'tanggal_mulai'   => 'nullable|date',
                'tanggal_selesai' => 'nullable|date',
                'manager_nama'    => 'nullable|string|max:255',
                'ttd_manager'     => 'nullable|string',
                'perusahaan_nama' => 'nullable|string|max:255',
                'ttd_perusahaan'  => 'nullable|string',
                'diverifikasi_nama' => 'nullable|string|max:255',
                'diverifikasi_signature' => 'nullable|string',
                'tenaga_kerja'    => 'nullable|json',
                'peralatan_kerja' => 'nullable|json',
                'apd'             => 'nullable|json',
            ])->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        // Handle signature uploads
        $validated['ttd_manager'] = $this->saveSignature($request->input('ttd_manager'), 'manager');
        $validated['ttd_perusahaan'] = $this->saveSignature($request->input('ttd_perusahaan'), 'perusahaan');
        $validated['diverifikasi_signature'] = $this->saveSignature($request->input('diverifikasi_signature'), 'verifikator');

        // JSON fields (default ke '[]' string JSON)
        $validated['tenaga_kerja'] = $request->input('tenaga_kerja') ?: '[]';
        $validated['peralatan_kerja'] = $request->input('peralatan_kerja') ?: '[]';
        $validated['apd'] = $request->input('apd') ?: '[]';

        // Simpan atau update
$dataKontraktor = DataKontraktor::updateOrCreate(

            ['notification_id' => $validated['notification_id']],
            array_filter([
                'nama_perusahaan' => $validated['nama_perusahaan'],
                'jenis_pekerjaan' => $validated['jenis_pekerjaan'],
                'lokasi_kerja'    => $validated['lokasi_kerja'],
                'tanggal_mulai'   => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'manager_nama'    => $validated['manager_nama'],
                'ttd_manager'     => $validated['ttd_manager'],
                'perusahaan_nama' => $validated['perusahaan_nama'],
                'ttd_perusahaan'  => $validated['ttd_perusahaan'],
                'diverifikasi_nama' => $validated['diverifikasi_nama'],
                'diverifikasi_signature' => $validated['diverifikasi_signature'],
                'tenaga_kerja'    => $validated['tenaga_kerja'],
                'peralatan_kerja' => $validated['peralatan_kerja'],
                'apd'             => $validated['apd'],
            ], fn($v) => $v !== null && $v !== '')
        );
// Generate token jika belum ada
if (!$dataKontraktor->token) {
    $dataKontraktor->token = \Str::uuid();
    $dataKontraktor->save();
}
        return back()->with('success', 'Data kontraktor berhasil disimpan!');
    }

    // Signature Helper
   private function saveSignature($input, $role)
{
    if (!$input) return null;

    // Sudah path lama (bukan base64)
    if (!str_starts_with($input, 'data:image')) return $input;

    // Base64 baru
    $folder = 'signatures/data-kontraktor/';
    $filename = $role . '_' . \Illuminate\Support\Str::random(10) . '.png';
    $path = storage_path('app/public/' . $folder);

    if (!file_exists($path)) mkdir($path, 0777, true);

    $image = str_replace('data:image/png;base64,', '', $input);
    $image = str_replace(' ', '+', $image);
    file_put_contents($path . $filename, base64_decode($image));

    return 'storage/' . $folder . $filename;
}


    public function showByToken($token)
{
    $dataKontraktor = DataKontraktor::where('token', $token)->firstOrFail();
    $notification = $dataKontraktor->notification; // Relasi jika ada

    return view('pengajuan-user.kontraktor.form', compact('dataKontraktor', 'notification'));
}
public function storeByToken(Request $request, $token)
{
    $dataKontraktor = DataKontraktor::where('token', $token)->firstOrFail();

    try {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'jenis_pekerjaan' => 'nullable|string|max:255',
            'lokasi_kerja'    => 'nullable|string|max:255',
            'tanggal_mulai'   => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'manager_nama'    => 'nullable|string|max:255',
            'ttd_manager'     => 'nullable|string',
            'perusahaan_nama' => 'nullable|string|max:255',
            'ttd_perusahaan'  => 'nullable|string',
            'diverifikasi_nama' => 'nullable|string|max:255',
            'diverifikasi_signature' => 'nullable|string',
            'tenaga_kerja'    => 'nullable|json',
            'peralatan_kerja' => 'nullable|json',
            'apd'             => 'nullable|json',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return back()->withErrors($e->errors())->withInput();
    }
 // Simpan tanda tangan base64 ke file
  $validated['ttd_manager'] = $this->saveSignature($request->input('ttd_manager'), 'manager') ?? $dataKontraktor->ttd_manager;
$validated['ttd_perusahaan'] = $this->saveSignature($request->input('ttd_perusahaan'), 'perusahaan') ?? $dataKontraktor->ttd_perusahaan;
$validated['diverifikasi_signature'] = $this->saveSignature($request->input('diverifikasi_signature'), 'verifikator') ?? $dataKontraktor->diverifikasi_signature;

    // Update Data
    $dataKontraktor->update($validated);

    return back()->with('success', 'Data Kontraktor berhasil disimpan.');
}

    public function previewPdf($id)
    {
        $data = DataKontraktor::where('notification_id', $id)->firstOrFail();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pengajuan-user.kontraktor.pdfdatakontraktor', compact('data'));
        return $pdf->stream('Form-Data-Kontraktor.pdf');
    }
}
