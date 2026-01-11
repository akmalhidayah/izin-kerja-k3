<form method="POST" action="{{ route('working-permit.pengangkatan.store') }}" enctype="multipart/form-data">
    @csrf
<input type="hidden" name="notification_id" value="{{ $notification->id ?? '' }}">
<input type="hidden" name="clear_all_signatures" id="clear_all_signatures" value="0">

<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h2 class="text-xl font-bold uppercase text-center">RENCANA PENGANGKATAN</h2>
    <p class="text-center text-sm text-gray-600 mb-4">
        Rencana pengangkatan dibuat sebelum memulai kegiatan pengangkatan, dibuat oleh Operator Pesawat Angkat dan Juru Ikat (Rigger) 
        dan disetujui oleh Lifting Permit Verificator.
    </p>

  <h3 class="font-bold bg-black text-white px-2 py-1 mt-4">1. Pesawat Angkat, Alat Bantu Angkat & Berat Beban</h3>

<table class="table-auto w-full border mt-3 text-sm">
    <tbody>
        <!-- Pesawat Angkat -->
        <tr class="bg-gray-100 font-bold">
            <td colspan="4" class="border px-2 py-1">Pesawat Angkat</td>
        </tr>
        <tr>
            <td class="border px-2 py-1 w-1/4">Jenis / Tipe</td>
            <td class="border px-2 py-1"><input type="text" name="jenis_tipe" class="input w-full" value="{{ old('jenis_tipe', $permit->jenis_tipe ?? '') }}"></td>
            <td class="border px-2 py-1">Maksimum panjang <em>boom</em> (crane)</td>
            <td class="border px-2 py-1"><input type="text" name="max_boom" class="input w-full" value="{{ old('max_boom', $permit->max_boom ?? '') }}"> (meter)</td>
        </tr>
        <tr>
            <td class="border px-2 py-1">Kapasitas maksimum</td>
            <td class="border px-2 py-1"><input type="text" name="kapasitas_maks" class="input w-full" value="{{ old('kapasitas_maks', $permit->kapasitas_maks ?? '') }}"> (ton)</td>
            <td class="border px-2 py-1">Minimum panjang <em>boom</em> (crane)</td>
            <td class="border px-2 py-1"><input type="text" name="min_boom" class="input w-full" value="{{ old('min_boom', $permit->min_boom ?? '') }}"> (meter)</td>
        </tr>
        <tr>
            <td class="border px-2 py-1"><em>SWL main block</em></td>
            <td class="border px-2 py-1"><input type="text" name="swl_main_block" class="input w-full" value="{{ old('swl_main_block', $permit->swl_main_block ?? '') }}"> (ton)</td>
            <td class="border px-2 py-1">Maksimum radius <em>boom</em> (crane)</td>
            <td class="border px-2 py-1"><input type="text" name="max_radius" class="input w-full" value="{{ old('max_radius', $permit->max_radius ?? '') }}"> (meter)</td>
        </tr>
        <tr>
            <td class="border px-2 py-1"><em>SWL auxiliary block</em></td>
            <td class="border px-2 py-1"><input type="text" name="swl_aux_block" class="input w-full" value="{{ old('swl_aux_block', $permit->swl_aux_block ?? '') }}"> (ton)</td>
            <td class="border px-2 py-1">Minimum radius <em>boom</em> (crane)</td>
            <td class="border px-2 py-1"><input type="text" name="min_radius" class="input w-full" value="{{ old('min_radius', $permit->min_radius ?? '') }}"> (meter)</td>
        </tr>

        <!-- Alat Bantu Angkat -->
        <tr class="bg-gray-100 font-bold">
            <td colspan="4" class="border px-2 py-1">Alat Bantu Angkat</td>
        </tr>
        <tr>
            <td class="border px-2 py-1"><em>SWL chain/wire rope/webbing sling</em></td>
            <td class="border px-2 py-1"><input type="text" name="swl_chain" class="input w-full" value="{{ old('swl_chain', $permit->swl_chain ?? '') }}"> (ton)</td>
            <td class="border px-2 py-1"><em>SWL master link / oblong ring</em></td>
            <td class="border px-2 py-1"><input type="text" name="swl_master_link" class="input w-full" value="{{ old('swl_master_link', $permit->swl_master_link ?? '') }}"> (ton)</td>
        </tr>
        <tr>
            <td class="border px-2 py-1"><em>SWL shackle</em></td>
            <td class="border px-2 py-1"><input type="text" name="swl_shackle" class="input w-full" value="{{ old('swl_shackle', $permit->swl_shackle ?? '') }}"> (ton)</td>
            <td class="border px-2 py-1"><em>SWL hammer lock</em></td>
            <td class="border px-2 py-1"><input type="text" name="swl_hammer_lock" class="input w-full" value="{{ old('swl_hammer_lock', $permit->swl_hammer_lock ?? '') }}"> (ton)</td>
        </tr>
        <tr>
            <td class="border px-2 py-1"><em>SWL spreader bar</em></td>
            <td class="border px-2 py-1"><input type="text" name="swl_spreader_bar" class="input w-full" value="{{ old('swl_spreader_bar', $permit->swl_spreader_bar ?? '') }}"> (ton)</td>
            <td class="border px-2 py-1"><em>SWL hook</em></td>
            <td class="border px-2 py-1"><input type="text" name="swl_hook" class="input w-full" value="{{ old('swl_hook', $permit->swl_hook ?? '') }}"> (ton)</td>
        </tr>
        <tr>
            <td class="border px-2 py-1"><em>SWL pulley</em></td>
            <td class="border px-2 py-1"><input type="text" name="swl_pulley" class="input w-full" value="{{ old('swl_pulley', $permit->swl_pulley ?? '') }}"> (ton)</td>
            <td class="border px-2 py-1"><em>SWL anchor point (hoist winch)</em></td>
            <td class="border px-2 py-1"><input type="text" name="swl_anchor" class="input w-full" value="{{ old('swl_anchor', $permit->swl_anchor ?? '') }}"> (ton)</td>
        </tr>

        <!-- Berat Beban -->
        <tr class="bg-gray-100 font-bold">
            <td colspan="4" class="border px-2 py-1">Berat Beban</td>
        </tr>
        <tr>
            <td class="border px-2 py-1">Berat beban</td>
            <td class="border px-2 py-1"><input type="text" name="berat_beban" class="input w-full" value="{{ old('berat_beban', $permit->berat_beban ?? '') }}"> (ton)</td>
            <td class="border px-2 py-1">Berat <em>man basket</em></td>
            <td class="border px-2 py-1"><input type="text" name="berat_man_basket" class="input w-full" value="{{ old('berat_man_basket', $permit->berat_man_basket ?? '') }}"> (ton)</td>
        </tr>
    </tbody>
</table>
</div>

<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        2. Teknik Pengikatan Beban dengan <em>Webbing Sling</em>
        <span class="italic text-xs font-normal">(beri tanda centang untuk teknik pengikatan yang akan dilakukan)</span>
    </h3>

    <div class="overflow-x-auto mt-3">
        <table class="table-auto border w-full text-center text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-2 py-1">Vertical</th>
                    <th class="border px-2 py-1">Choker</th>
                    <th class="border px-2 py-1">Basket 90°</th>
                    <th class="border px-2 py-1">Basket 60°</th>
                    <th class="border px-2 py-1">Basket 45°</th>
                    <th class="border px-2 py-1">Basket 30°</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                   @php
    $selectedWebbing = old('teknik_pengikatan') 
        ?? (isset($permit) && $permit && $permit->teknik_pengikatan 
            ? (is_array($permit->teknik_pengikatan) 
                ? $permit->teknik_pengikatan 
                : json_decode($permit->teknik_pengikatan, true)) 
            : []);
@endphp

                    @for ($i = 1; $i <= 6; $i++)
                        <td class="border px-2 py-2">
                            <img src="{{ asset('images/1.' . $i . '.png') }}" alt="Sling {{ $i }}" class="mx-auto h-16">
                            <div class="mt-1">
                                <input type="checkbox" name="teknik_pengikatan[]" value="1.{{ $i }}"
                                    {{ in_array('1.'.$i, $selectedWebbing) ? 'checked' : '' }}> Pilih
                            </div>
                        </td>
                    @endfor
                </tr>
                <tr class="bg-gray-50">
                    <td class="border px-2 py-1">1x SWL</td>
                    <td class="border px-2 py-1">0.75x SWL</td>
                    <td class="border px-2 py-1">2x SWL</td>
                    <td class="border px-2 py-1">1.7x SWL</td>
                    <td class="border px-2 py-1">1.4x SWL</td>
                    <td class="border px-2 py-1">1x SWL</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        3. Teknik Pengikatan Beban dengan <em>Wire Rope Sling</em>
        <span class="italic text-xs font-normal">(beri tanda centang untuk teknik pengikatan yang akan dilakukan)</span>
    </h3>

    <div class="overflow-x-auto mt-3">
        <table class="table-auto border w-full text-center text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-2 py-1">Vertical</th>
                    <th class="border px-2 py-1">Choker</th>
                    <th class="border px-2 py-1">Double Leg 90°</th>
                    <th class="border px-2 py-1">Double Leg 60°</th>
                    <th class="border px-2 py-1">Double Leg 45°</th>
                    <th class="border px-2 py-1">Double Leg 30°</th>
                </tr>
            </thead>
            <tbody>
                <tr>
@php
    $selectedWire = old('wire_rope_sling')
        ?? (isset($permit) && $permit && $permit->wire_rope_sling
            ? (is_array($permit->wire_rope_sling)
                ? $permit->wire_rope_sling
                : json_decode($permit->wire_rope_sling, true))
            : []);

    // ⬇ Tambahkan ini
    $wire = ['2.1.png', '2.2.png', '2.3.jpg', '2.4.png', '2.5.png', '2.6.png'];
@endphp


                    @foreach ($wire as $i => $img)
                        <td class="border px-2 py-2">
                            <img src="{{ asset('images/' . $img) }}" alt="Wire {{ $i + 1 }}" class="mx-auto h-16">
                            <div class="mt-1">
                                <input type="checkbox" name="wire_rope_sling[]" value="2.{{ $i + 1 }}"
                                    {{ in_array('2.'.($i + 1), $selectedWire) ? 'checked' : '' }}> Pilih
                            </div>
                        </td>
                    @endforeach
                </tr>
                <tr class="bg-gray-50">
                    <td class="border px-2 py-1">1x SWL</td>
                    <td class="border px-2 py-1">0.75x SWL</td>
                    <td class="border px-2 py-1">2x SWL</td>
                    <td class="border px-2 py-1">1.7x SWL</td>
                    <td class="border px-2 py-1">1.4x SWL</td>
                    <td class="border px-2 py-1">1x SWL</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">4. Gambar/Sketsa Pengangkatan</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
        <div>
            <label class="font-semibold block mb-2">Diagram konfigurasi pesawat angkat:</label>

            {{-- Tampilkan jika ada gambar --}}
            @if (!empty($permit->diagram_pesawat))
                <img src="{{ asset($permit->diagram_pesawat) }}" alt="Diagram Pesawat" class="mb-2 max-h-48 mx-auto border">
            @endif

            <input type="file" name="diagram_pesawat" accept="image/*" class="block w-full text-sm text-gray-700 
                file:mr-4 file:py-2 file:px-4
                file:rounded-full file:border-0
                file:text-sm file:font-semibold
                file:bg-blue-50 file:text-blue-700
                hover:file:bg-blue-100">
        </div>

        <div>
            <label class="font-semibold block mb-2">Diagram konfigurasi pengikatan beban:</label>

            {{-- Tampilkan jika ada gambar --}}
            @if (!empty($permit->diagram_pengikatan))
                <img src="{{ asset($permit->diagram_pengikatan) }}" alt="Diagram Pengikatan" class="mb-2 max-h-48 mx-auto border">
            @endif

            <input type="file" name="diagram_pengikatan" accept="image/*" class="block w-full text-sm text-gray-700 
                file:mr-4 file:py-2 file:px-4
                file:rounded-full file:border-0
                file:text-sm file:font-semibold
                file:bg-blue-50 file:text-blue-700
                hover:file:bg-blue-100">
        </div>
    </div>
</div>
<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">5. Persetujuan Rencana Pengangkatan</h3>
    <p class="mt-2 mb-4 text-sm">
        Saya menyatakan bahwa rencana pengangkatan ini sudah sesuai dan aman untuk melakukan pengangkatan.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- Rigger --}}
        <div>
            <label class="block font-semibold mb-1">Dibuat oleh Rigger:</label>
            <input type="text" name="rigger_name"
                value="{{ old('rigger_name', $permit->rigger_name ?? '') }}"
                class="input w-full border rounded px-2 py-1 text-sm mb-1">

            @php
                $riggerSign = old('signature_rigger', $permit->signature_rigger ?? null);
            @endphp

            @if ($riggerSign && !Str::startsWith($riggerSign, 'data:image') && file_exists(public_path($riggerSign)))
                <img src="{{ asset($riggerSign) }}" alt="Tanda Tangan Rigger" class="h-16 mx-auto mb-1 border">
            @endif

            <button type="button"
                onclick="openSignPad('pengangkatan_signature_rigger')"
                class="text-blue-600 underline text-xs mt-1">
                {{ $riggerSign ? 'Ubah Tanda Tangan' : 'Tanda Tangan' }}
            </button>

            <input type="hidden" name="signature_rigger" id="pengangkatan_signature_rigger"
                value="{{ old('signature_rigger', $riggerSign) }}">
        </div>

        {{-- Operator --}}
        <div>
            <label class="block font-semibold mb-1">Dibuat oleh Operator:</label>
            <input type="text" name="operator_name"
                value="{{ old('operator_name', $permit->operator_name ?? '') }}"
                class="input w-full border rounded px-2 py-1 text-sm mb-1">

            @php
                $operatorSign = old('signature_operator', $permit->signature_operator ?? null);
            @endphp

            @if ($operatorSign && !Str::startsWith($operatorSign, 'data:image') && file_exists(public_path($operatorSign)))
                <img src="{{ asset($operatorSign) }}" alt="Tanda Tangan Operator" class="h-16 mx-auto mb-1 border">
            @endif

            <button type="button"
                onclick="openSignPad('pengangkatan_signature_operator')"
                class="text-blue-600 underline text-xs mt-1">
                {{ $operatorSign ? 'Ubah Tanda Tangan' : 'Tanda Tangan' }}
            </button>

            <input type="hidden" name="signature_operator" id="pengangkatan_signature_operator"
                value="{{ old('signature_operator', $operatorSign) }}">
        </div>

        {{-- Verificator --}}
        <div>
            <label class="block font-semibold mb-1">Disetujui oleh Lifting Permit Verificator:</label>
            <input type="text" name="verificator_name"
                value="{{ old('verificator_name', $permit->verificator_name ?? '') }}"
                class="input w-full border rounded px-2 py-1 text-sm mb-1">

            @php
                $verificatorSign = old('signature_verificator', $permit->signature_verificator ?? null);
            @endphp

            @if ($verificatorSign && !Str::startsWith($verificatorSign, 'data:image') && file_exists(public_path($verificatorSign)))
                <img src="{{ asset($verificatorSign) }}" alt="Tanda Tangan Verificator" class="h-16 mx-auto mb-1 border">
            @endif

            <button type="button"
                onclick="openSignPad('pengangkatan_signature_verificator')"
                class="text-blue-600 underline text-xs mt-1">
                {{ $verificatorSign ? 'Ubah Tanda Tangan' : 'Tanda Tangan' }}
            </button>

            <input type="hidden" name="signature_verificator" id="pengangkatan_signature_verificator"
                value="{{ old('signature_verificator', $verificatorSign) }}">
        </div>
    </div>
</div>

<!-- Tombol Simpan -->
<div class="flex justify-center gap-3 mt-8">
    <button type="button"
        onclick="if (confirm('Hapus semua tanda tangan?')) { document.getElementById('clear_all_signatures').value = '1'; this.closest('form').submit(); }"
        class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded shadow-md transition duration-200">
        Hapus Semua TTD
    </button>
    <button type="submit" name="action" value="save"
        class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-6 rounded shadow-md transition duration-200">
        ?? Simpan
    </button>
</div>
</form>