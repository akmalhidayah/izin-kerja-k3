<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{

    public function store(Request $request)
    {
        if (auth()->user()?->isPgo()) {
            return back()->with('error', 'Akses ditolak untuk akun PGO.');
        }

        try {
            $request->validate([
                'notification_id' => 'required|exists:notifications,id',
                'step_name' => 'required|string|max:255',
                'file' => 'required|file|mimes:pdf,jpg,png|max:5120',
            ]);

            $notification = $this->findAccessibleNotification($request->notification_id);

            if (!$notification) {
                return back()->with('error', 'Notifikasi tidak valid atau tidak dapat diakses.');
            }

            $filePath = $request->file('file')->store('uploads', 'public');

            Upload::create([
                'notification_id' => $request->notification_id,
                'step' => $request->step_name,
                'file_path' => $filePath,
                'status' => 'pending',
            ]);
            

            return back()->with('success', 'File berhasil diupload!');
        } catch (\Exception $e) {
            \Log::error('Upload error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat upload: ' . $e->getMessage());
        }
    }
    public function destroy($id)
{
    if (auth()->user()?->isPgo()) {
        return back()->with('error', 'Akses ditolak untuk akun PGO.');
    }

    $upload = Upload::with('notification')->findOrFail($id);

    if (!$this->findAccessibleNotification($upload->notification_id)) {
        abort(403, 'Akses ditolak.');
    }

    if ($upload->file_path && Storage::disk('public')->exists($upload->file_path)) {
        Storage::disk('public')->delete($upload->file_path);
    }

    $upload->delete();

    return back()->with('success', 'File berhasil dihapus!');
}


    public function updateStatus(Request $request, $id)
    {
        if (auth()->user()?->isPgo()) {
            return back()->with('error', 'Akses ditolak untuk akun PGO.');
        }

        $request->validate([
            'status' => 'required|in:pending,done,revisi',
        ]);

        $upload = Upload::with('notification')->findOrFail($id);

        if (!$this->findAccessibleNotification($upload->notification_id)) {
            abort(403, 'Akses ditolak.');
        }

        $upload->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status berhasil diperbarui!');
    }
}
