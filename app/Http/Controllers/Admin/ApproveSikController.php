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
    private function deleteStoredSignature(?string $path): void
    {
        if (!$path || !is_string($path)) {
            return;
        }

        $relativePath = str_starts_with($path, 'storage/')
            ? substr($path, strlen('storage/'))
            : $path;

        Storage::disk('public')->delete($relativePath);
    }

    private function storeSignatureForRole(Request $request, $id, string $column, string $filePrefix)
    {
        $request->validate([
            'signature' => 'required|string',
        ]);

        $stepApproval = StepApproval::where('notification_id', $id)
            ->where('step', 'sik')
            ->first();

        if (!$stepApproval || ($stepApproval->status ?? null) !== 'disetujui') {
            return back()->with('error', 'Step SIK belum disetujui atau tidak ditemukan.');
        }

        $path = $this->saveSignature($request->signature, $filePrefix);

        if (!$path) {
            return back()->with('error', 'Gagal menyimpan tanda tangan!');
        }

        if (!empty($stepApproval->$column)) {
            $this->deleteStoredSignature($stepApproval->$column);
        }

        $stepApproval->$column = $path;
        $stepApproval->save();

        return redirect()->back()->with('success', 'Tanda tangan berhasil disimpan.');
    }

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
        return $this->storeSignatureForRole($request, $id, 'signature_senior_manager', 'senior_manager');
    }

    public function storeManagerSignature(Request $request, $id)
    {
        return $this->storeSignatureForRole($request, $id, 'signature_manager', 'manager');
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

        $raw = base64_decode($image);
        if ($raw === false) {
            return null;
        }

        $img = @imagecreatefromstring($raw);
        if ($img === false) {
            file_put_contents($path . $filename, $raw);
            return 'storage/' . $folder . $filename;
        }

        $cropped = $this->cropTransparentPng($img);
        if ($cropped) {
            imagedestroy($img);
            $img = $cropped;
        }

        imagepng($img, $path . $filename);
        imagedestroy($img);

        return 'storage/' . $folder . $filename;
    }

    private function cropTransparentPng($img)
    {
        $width = imagesx($img);
        $height = imagesy($img);

        $minX = $width;
        $minY = $height;
        $maxX = 0;
        $maxY = 0;
        $found = false;

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgba = imagecolorat($img, $x, $y);
                $alpha = ($rgba & 0x7F000000) >> 24;
                if ($alpha < 127) {
                    $found = true;
                    if ($x < $minX) $minX = $x;
                    if ($y < $minY) $minY = $y;
                    if ($x > $maxX) $maxX = $x;
                    if ($y > $maxY) $maxY = $y;
                }
            }
        }

        if (!$found) {
            return null;
        }

        $pad = 2;
        $minX = max(0, $minX - $pad);
        $minY = max(0, $minY - $pad);
        $maxX = min($width - 1, $maxX + $pad);
        $maxY = min($height - 1, $maxY + $pad);

        $newW = $maxX - $minX + 1;
        $newH = $maxY - $minY + 1;

        $newImg = imagecreatetruecolor($newW, $newH);
        imagealphablending($newImg, false);
        imagesavealpha($newImg, true);
        $transparent = imagecolorallocatealpha($newImg, 0, 0, 0, 127);
        imagefilledrectangle($newImg, 0, 0, $newW, $newH, $transparent);

        imagecopy($newImg, $img, 0, 0, $minX, $minY, $newW, $newH);

        return $newImg;
    }
}
