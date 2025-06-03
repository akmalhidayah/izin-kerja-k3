<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use App\Models\Notification;
use Illuminate\Http\Request;

class UploadController extends Controller
{

    public function store(Request $request)
    {
        try {
            $request->validate([
                'notification_id' => 'required|exists:notifications,id',
                'step_name' => 'required|string|max:255',
                'file' => 'required|file|mimes:pdf,jpg,png|max:2048',
            ]);

            $filePath = $request->file('file')->store('uploads', 'public');

            Upload::create([
                'notification_id' => $request->notification_id,
                'step' => $request->step_name,                  'file_path' => $filePath,
                'status' => 'pending',
            ]);
            

            return back()->with('success', 'File berhasil diupload!');
        } catch (\Exception $e) {
            \Log::error('Upload error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat upload: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,done,revisi',
        ]);

        $upload = Upload::findOrFail($id);
        $upload->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status berhasil diperbarui!');
    }
}
