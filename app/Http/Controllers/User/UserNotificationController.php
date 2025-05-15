<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class UserNotificationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:po,spk,notif',
            'number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        // Simpan file jika ada
        if ($request->hasFile('file')) {
            $validated['file'] = $request->file('file')->store('notifications', 'public');
        }

        // Kalau type SPK, generate nomor otomatis
        if ($validated['type'] === 'spk') {
            $validated['number'] = 'SPK-' . now()->format('YmdHis');
        }

Notification::create([
    'type' => $validated['type'],
    'number' => $validated['number'] ?? null,
    'description' => $validated['description'] ?? null,
    'file' => $validated['file'] ?? null,
    'user_id' => auth()->id(), // 🔥 Tambahkan ini!
]);


        return back()->with('success', 'Notifikasi berhasil disimpan!');
    }
}
