<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\StepApproval;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ApproveSikController extends Controller
{
  public function index(Request $request)
{
    $query = Notification::with(['user', 'assignedAdmin', 'sikStep']);

    if ($request->filled('bulan')) {
        $query->whereMonth('created_at', $request->bulan);
    } else {
        $query->whereMonth('created_at', now()->month);
    }

    if ($request->filled('tahun')) {
        $query->whereYear('created_at', $request->tahun);
    } else {
        $query->whereYear('created_at', now()->year);
    }

    $permintaansik = $query->latest()->get();

    return view('admin.approvesik', compact('permintaansik'));
}

    public function storeSignature(Request $request, $id)
    {
         \Log::info('Signature Received:', $request->all());
        $request->validate([
            'signature' => 'required|string',
        ]);

        $stepApproval = StepApproval::where('notification_id', $id)
            ->where('step', 'sik')
            ->first();

        if (!$stepApproval) {
            return back()->with('error', 'Step SIK belum disetujui atau tidak ditemukan.');
        }

        $path = $this->saveSignature($request->signature, 'senior_manager');

        if (!$path) {
            return back()->with('error', 'Gagal menyimpan tanda tangan!');
        }

        $stepApproval->signature_senior_manager = $path;
        $stepApproval->save();

        return redirect()->route('admin.approvesik.index')->with('success', '✅ Tanda tangan berhasil disimpan.');
    }

    private function saveSignature($base64, $role)
    {
        if (!$base64 || !str_starts_with($base64, 'data:image')) return null;

        $folder = 'signatures/sik/';
        $filename = $role . '_' . Str::random(10) . '.png';
        $path = storage_path('app/public/' . $folder);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $image = str_replace('data:image/png;base64,', '', $base64);
        $image = str_replace(' ', '+', $image);

        file_put_contents($path . $filename, base64_decode($image));

        return 'storage/' . $folder . $filename;
    }
}
