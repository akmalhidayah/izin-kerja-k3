<?php

namespace App\Http\Controllers\User\WorkingPermit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\WorkPermitPengangkatan;

class PengangkatanPermitController extends Controller
{
public function store(Request $request)
{
    try {
        $validated = Validator::make($request->all(), [
            'notification_id' => 'required|integer|exists:notifications,id',

            // Bagian 1
            'jenis_tipe' => 'nullable|string',
            'max_boom' => 'nullable|string',
            'min_boom' => 'nullable|string',
            'kapasitas_maks' => 'nullable|string',
            'swl_main_block' => 'nullable|string',
            'swl_aux_block' => 'nullable|string',
            'max_radius' => 'nullable|string',
            'min_radius' => 'nullable|string',
            'swl_chain' => 'nullable|string',
            'swl_master_link' => 'nullable|string',
            'swl_shackle' => 'nullable|string',
            'swl_hammer_lock' => 'nullable|string',
            'swl_spreader_bar' => 'nullable|string',
            'swl_hook' => 'nullable|string',
            'swl_pulley' => 'nullable|string',
            'swl_anchor' => 'nullable|string',
            'berat_beban' => 'nullable|string',
            'berat_man_basket' => 'nullable|string',

            // Bagian 2-3
            'teknik_pengikatan' => 'nullable|array',
            'wire_rope_sling' => 'nullable|array',

            // Bagian 4
            'diagram_pesawat' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'diagram_pengikatan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            // Bagian 5
            'rigger_name' => 'nullable|string',
            'signature_rigger' => 'nullable|string',
            'operator_name' => 'nullable|string',
            'signature_operator' => 'nullable|string',
            'verificator_name' => 'nullable|string',
            'signature_verificator' => 'nullable|string',
        ])->validate();
    } catch (ValidationException $e) {
        return back()->withErrors($e->errors())->withInput();
    }

    $validated['teknik_pengikatan'] = json_encode($request->input('teknik_pengikatan', []));
    $validated['wire_rope_sling'] = json_encode($request->input('wire_rope_sling', []));

    // Ambil data lama dari database
    $existing = WorkPermitPengangkatan::where('notification_id', $validated['notification_id'])->first();

    // Tanda tangan: simpan baru jika base64, kalau tidak fallback ke yang lama
    $validated['signature_rigger'] = $this->saveSignature($request->input('signature_rigger'), 'rigger')
        ?? ($existing->signature_rigger ?? null);

    $validated['signature_operator'] = $this->saveSignature($request->input('signature_operator'), 'operator')
        ?? ($existing->signature_operator ?? null);

    $validated['signature_verificator'] = $this->saveSignature($request->input('signature_verificator'), 'verificator')
        ?? ($existing->signature_verificator ?? null);

    // Upload diagram
    if ($request->hasFile('diagram_pesawat')) {
        $path = $request->file('diagram_pesawat')->store('uploads/diagram', 'public');
        $validated['diagram_pesawat'] = 'storage/' . $path;
    }
    if ($request->hasFile('diagram_pengikatan')) {
        $path = $request->file('diagram_pengikatan')->store('uploads/diagram', 'public');
        $validated['diagram_pengikatan'] = 'storage/' . $path;
    }

    // Simpan atau update
    $permit = WorkPermitPengangkatan::updateOrCreate(
        ['notification_id' => $validated['notification_id']],
        array_merge($validated, ['token' => $existing->token ?? Str::uuid()])
    );

    return back()->with('success', 'Data Working Permit Pengangkatan berhasil disimpan!');
}


    public function showByToken($token)
    {
        $permit = WorkPermitPengangkatan::where('token', $token)->with('notification')->firstOrFail();

        return view('pengajuan-user.workingpermit.form-token-pengangkatan', [
            'permit' => $permit,
            'notification' => $permit->notification,
            'jenis' => 'pengangkatan',
        ]);
    }

    public function storeByToken(Request $request, $token)
    {
        $permit = WorkPermitPengangkatan::where('token', $token)->firstOrFail();
        $request->merge(['notification_id' => $permit->notification_id]);

        app()->call([$this, 'store'], ['request' => $request]);

        session()->flash('alert', 'Data berhasil disimpan melalui token!');
        return back();
    }

    private function saveSignature($base64, $role)
    {
        if (!$base64 || !str_starts_with($base64, 'data:image')) return null;

        $folder = 'signatures/working-permit/pengangkatan/';
        $filename = $role . '_' . Str::random(10) . '.png';
        $path = storage_path('app/public/' . $folder);

        if (!file_exists($path)) mkdir($path, 0777, true);

        $image = str_replace('data:image/png;base64,', '', $base64);
        $image = str_replace(' ', '+', $image);
        file_put_contents($path . $filename, base64_decode($image));

        return 'storage/' . $folder . $filename;
    }
}
