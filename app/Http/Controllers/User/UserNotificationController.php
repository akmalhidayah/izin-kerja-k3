<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class UserNotificationController extends Controller
{
    public function store(Request $request)
    {
        // Validasi awal
        $validated = $request->validate([
            'type' => 'required|in:po,spk,notif',
            'number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        // Validasi tambahan
        if (in_array($validated['type'], ['po', 'notif']) && empty($validated['number'])) {
            return back()->withErrors([
                'number' => 'Nomor wajib diisi untuk jenis ' . strtoupper($validated['type'])
            ])->withInput();
        }

        // Nomor otomatis untuk SPK
        if ($validated['type'] === 'spk') {
            $validated['number'] = 'SPK-' . now()->format('YmdHis');
        }

        // Upload file jika ada
        if ($request->hasFile('file')) {
            $validated['file'] = $request->file('file')->store('notifications', 'public');
        }

        // ⛔ HAPUS LOGIC UPDATE —> Langsung buat baru saja
        Notification::create([
            'type' => $validated['type'],
            'number' => $validated['number'] ?? null,
            'description' => $validated['description'] ?? null,
            'file' => $validated['file'] ?? null,
            'user_id' => auth()->id(),
            'status' => 'menunggu',
        ]);

        return back()->with('success', 'Notifikasi berhasil disimpan!');
    }

}
