<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Jsa;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

        $notification = $this->findAccessibleNotification($validated['notification_id']);

        if (!$notification) {
            return back()->with('error', 'Notifikasi tidak valid.');
        }

        // Signature Handling
        $validated['dibuat_signature'] = $this->saveSignature($request->input('dibuat_signature'), 'dibuat');
        $validated['disetujui_signature'] = $this->saveSignature($request->input('disetujui_signature'), 'disetujui');
        $validated['diverifikasi_signature'] = $this->saveSignature($request->input('diverifikasi_signature'), 'diverifikasi');

        // Langkah Kerja JSON
        $validated['langkah_kerja'] = $request->input('langkah_kerja') ?: '[]';

        $jsa = null;

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                $jsa = DB::transaction(function () use ($validated) {
                    $validated['no_jsa'] = $this->nextJsaNumber(true);

                    return Jsa::create($validated);
                });

                break;
            } catch (QueryException $e) {
                if ($attempt >= 3 || $e->getCode() !== '23000') {
                    throw $e;
                }

                usleep(50000);
            }
        }

        $this->ensurePermitToken($jsa);

        return back()->with('success', 'Data JSA berhasil disimpan!');
    }

    public function edit($id)
    {
        $jsa = Jsa::findOrFail($id);
        $this->abortUnlessCanAccessNotification($jsa->notification_id);

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
        $notification = $this->findAccessibleNotification($jsa->notification_id);

        if (!$notification) {
            return back()->with('error', 'Notifikasi tidak valid.');
        }

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
        $this->abortIfPermitTokenExpired($jsa);
        return view('pengajuan-user.jsa.form', compact('jsa'));
    }

    public function storeByToken(Request $request, $token)
    {
        $jsa = Jsa::where('token', $token)->firstOrFail();
        $this->abortIfPermitTokenExpired($jsa);

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

        $message = 'Data JSA berhasil disimpan.';

        return back()
            ->with('success', $message)
            ->with('token_saved', $message)
            ->with('token_pdf_url', route('token-pdf.show', ['type' => 'jsa', 'token' => $jsa->token]));
    }

    public function showPdf($notification_id)
    {
        if (!$this->tokenPdfAccessAllowed()) {
            $this->abortUnlessCanAccessNotification($notification_id);
        }

        $jsa = Jsa::where('notification_id', $notification_id)->firstOrFail();
        $langkahKerja = $jsa->langkah_kerja;

        if (is_string($langkahKerja)) {
            $decoded = json_decode($langkahKerja, true);
            $langkahKerja = is_array($decoded) ? $decoded : [];
        }

        $jsa->langkah_kerja = is_array($langkahKerja) ? $langkahKerja : [];

        $pdf = Pdf::loadView('pengajuan-user.jsa.pdfjsa', compact('jsa'))
            ->setPaper('a4', 'landscape');
        $filename = 'jsa_' . str_replace(['/', '\\'], '_', $jsa->no_jsa) . '.pdf';
        return $pdf->stream($filename);
    }

    public function downloadPdf($notification_id)
    {
        return $this->showPdf($notification_id);
    }

    private function saveSignature($base64, $role)
    {
        return $this->saveBase64PngSignature($base64, $role, 'signatures/jsa/');
    }

    public function getGeneratedNoJsa()
    {
        return $this->nextJsaNumber();
    }

    /**
     * Generate the next global JSA number for the current month/year.
     *
     * Number values are parsed in PHP so this remains compatible with both
     * MySQL and SQLite, and malformed legacy values do not break numbering.
     */
    private function nextJsaNumber(bool $lock = false): string
    {
        $bulanTahun = now()->format('mY');
        $prefix = "JSA/ST/{$bulanTahun}";

        $query = Jsa::query()
            ->where('no_jsa', 'like', "%/{$prefix}");

        if ($lock) {
            $query->lockForUpdate();
        }

        $maxNumber = 0;
        $maxInteger = (string) PHP_INT_MAX;

        foreach ($query->pluck('no_jsa') as $noJsa) {
            if (!is_string($noJsa)) {
                continue;
            }

            [$numberPart, $suffix] = array_pad(explode('/', $noJsa, 2), 2, null);

            if ($suffix !== $prefix || $numberPart === '' || !ctype_digit($numberPart)) {
                continue;
            }

            // Ignore numeric values that cannot be represented by PHP's int.
            $normalizedNumber = ltrim($numberPart, '0');
            $normalizedNumber = $normalizedNumber === '' ? '0' : $normalizedNumber;

            if (
                strlen($normalizedNumber) > strlen($maxInteger)
                || (
                    strlen($normalizedNumber) === strlen($maxInteger)
                    && strcmp($normalizedNumber, $maxInteger) > 0
                )
            ) {
                continue;
            }

            $maxNumber = max($maxNumber, (int) $normalizedNumber);
        }

        return str_pad($maxNumber + 1, 3, '0', STR_PAD_LEFT) . "/$prefix";
    }
}
