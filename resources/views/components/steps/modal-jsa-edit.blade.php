{{-- ALERT SUCCESS / ERROR --}}
@if(session('success'))
    <div class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-4 py-2 rounded shadow z-50">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-500 text-white px-4 py-2 rounded shadow z-50">
        {{ implode(', ', $errors->all()) }}
    </div>
@endif

{{-- WRAP X-DATA DI LUAR --}}
<div x-data="formJSA({{ $jsa->langkah_kerja ? $jsa->langkah_kerja : '[]' }})"
x-init="console.log('✅ langkahKerja loaded (edit):', langkahKerja)">
    {{-- MODAL EDIT --}}
    <div 
        x-show="activeModal === '{{ $id }}'" 
        x-cloak 
        class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center"
    >
        <div 
            class="bg-white p-6 rounded-lg shadow-md w-full max-w-5xl overflow-y-auto max-h-[90vh]"
            @click.away="activeModal = null"
        >
            <h2 class="text-lg font-semibold mb-4">Edit Job Safety Analysis</h2>

            <form method="POST" action="{{ route('jsa.update', $jsa->id) }}" @submit.prevent="serializeLangkah(); $el.submit()">
                @csrf
                @method('PATCH')

                {{-- HIDDEN INPUT --}}
                <input type="hidden" name="notification_id" value="{{ $notification->id ?? '' }}">
                <input type="hidden" name="dibuat_signature" id="dibuat_signature" value="{{ $jsa->dibuat_signature ?? '' }}">
                <input type="hidden" name="disetujui_signature" id="disetujui_signature" value="{{ $jsa->disetujui_signature ?? '' }}">
                <input type="hidden" name="diverifikasi_signature" id="diverifikasi_signature" value="{{ $jsa->diverifikasi_signature ?? '' }}">
                <input type="hidden" name="langkah_kerja" id="langkah_kerja">

                {{-- HEADER --}}
                <table class="table-auto w-full border text-xs mb-2">
                    <tr>
                        <td class="border px-2 py-1 font-semibold">Nama Perusahaan</td>
                        <td class="border px-2 py-1" colspan="3">
                            <input type="text" name="nama_perusahaan" class="input w-full text-xs" value="{{ old('nama_perusahaan', $jsa->nama_perusahaan ?? '') }}">
                        </td>
                    </tr>
                    <tr>
                        <td class="border px-2 py-1 font-semibold">Job Safety Analysis No</td>
                       <td class="border px-2 py-1">
    <input type="text" name="no_jsa" class="input w-full text-xs" value="{{ old('no_jsa', $jsa->no_jsa ?? '') }}" readonly>
</td>

                        <td class="border px-2 py-1 font-semibold">Nama JSA</td>
                        <td class="border px-2 py-1">
                            <input type="text" name="nama_jsa" class="input w-full text-xs" value="{{ old('nama_jsa', $jsa->nama_jsa ?? '') }}">
                        </td>
                    </tr>
                    <tr>
                        <td class="border px-2 py-1 font-semibold">Departemen</td>
                        <td class="border px-2 py-1">
                            <input type="text" name="departemen" class="input w-full text-xs" value="{{ old('departemen', $jsa->departemen ?? '') }}">
                        </td>
                        <td class="border px-2 py-1 font-semibold">Area Kerja</td>
                        <td class="border px-2 py-1">
                            <input type="text" name="area_kerja" class="input w-full text-xs" value="{{ old('area_kerja', $jsa->area_kerja ?? '') }}">
                        </td>
                    </tr>
                    <tr>
                        <td class="border px-2 py-1 font-semibold">Tanggal</td>
                        <td class="border px-2 py-1" colspan="3">
                            <input type="date" name="tanggal" class="input w-full text-xs" value="{{ old('tanggal', isset($jsa->tanggal) ? $jsa->tanggal->format('Y-m-d') : '') }}">
                        </td>
                    </tr>
                </table>

               {{-- Dibuat/Disetujui/Diverifikasi --}}
<table class="table-auto w-full border text-xs mb-2">
    <tr class="text-center font-semibold">
        <td class="border px-2 py-1">Dibuat oleh</td>
        <td class="border px-2 py-1">Disetujui oleh</td>
        <td class="border px-2 py-1">Diverifikasi oleh</td>
    </tr>
    <tr>
        {{-- Dibuat oleh --}}
        <td class="border px-2 py-4 text-center align-top">
            <input type="text" 
                   name="dibuat_nama" 
                   class="w-full text-xs border-gray-300 rounded p-1 mb-2" 
                   placeholder="Masukkan Nama" 
                   value="{{ old('dibuat_nama', $jsa->dibuat_nama ?? '') }}">
            <button type="button" onclick="openSignPad('dibuat_signature')" class="text-blue-600 underline text-xs mt-1">
                Tanda Tangan
            </button>
            @if(!empty($jsa->dibuat_signature))
                <div class="flex justify-center mt-2">
                    <img src="{{ asset($jsa->dibuat_signature) }}" class="h-12" alt="TTD Dibuat">
                </div>
            @endif
        </td>

        {{-- Disetujui oleh --}}
        <td class="border px-2 py-4 text-center align-top">
            <input type="text" 
                   name="disetujui_nama" 
                   class="w-full text-xs border-gray-300 rounded p-1 mb-2" 
                   placeholder="Masukkan Nama" 
                   value="{{ old('disetujui_nama', $jsa->disetujui_nama ?? '') }}">
            <button type="button" onclick="openSignPad('disetujui_signature')" class="text-blue-600 underline text-xs mt-1">
                Tanda Tangan
            </button>
            @if(!empty($jsa->disetujui_signature))
                <div class="flex justify-center mt-2">
                    <img src="{{ asset($jsa->disetujui_signature) }}" class="h-12" alt="TTD Disetujui">
                </div>
            @endif
        </td>

        {{-- Diverifikasi oleh --}}
        <td class="border px-2 py-4 text-center align-top">
            <input type="text" 
                   name="diverifikasi_nama" 
                   class="w-full text-xs border-gray-300 rounded p-1 mb-2" 
                   placeholder="Masukkan Nama" 
                   value="{{ old('diverifikasi_nama', $jsa->diverifikasi_nama ?? '') }}">
            <button type="button" onclick="openSignPad('diverifikasi_signature')" class="text-blue-600 underline text-xs mt-1">
                Tanda Tangan
            </button>
            @if(!empty($jsa->diverifikasi_signature))
                <div class="flex justify-center mt-2">
                    <img src="{{ asset($jsa->diverifikasi_signature) }}" class="h-12" alt="TTD Diverifikasi">
                </div>
            @endif
        </td>
    </tr>
</table>

                {{-- LANGKAH KERJA --}}
                <table class="table-auto w-full border text-xs mb-2">
                    <thead class="bg-gray-100">
                        <tr class="text-center font-semibold">
                            <th class="border px-2 py-1 w-12">No</th>
                            <th class="border px-2 py-1">Urutan Langkah Kerja</th>
                            <th class="border px-2 py-1">Bahaya/Risiko</th>
                            <th class="border px-2 py-1">Pengendalian</th>
                            <th class="border px-2 py-1 w-16">Aksi</th>
                        </tr>
                    </thead>
                   <tbody>
    <template x-for="(row, index) in langkahKerja" :key="index">
        <tr>
            <td class="border px-2 py-1 text-center" x-text="index + 1"></td>
            
            <td class="border px-2 py-1">
                <textarea x-model="row.langkah" class="input w-full text-xs resize-y min-h-[60px]"></textarea>
            </td>
            
            <td class="border px-2 py-1">
                <textarea x-model="row.bahaya" class="input w-full text-xs resize-y min-h-[60px]"></textarea>
            </td>
            
            <td class="border px-2 py-1">
                <textarea x-model="row.pengendalian" class="input w-full text-xs resize-y min-h-[60px]"></textarea>
            </td>
            
            <td class="border px-2 py-1 text-center">
                <button type="button" @click="hapusRow(index)" class="text-red-500 text-xs">Hapus</button>
            </td>
        </tr>
    </template>
</tbody>

                </table>

                <button type="button" @click="tambahRow()" class="bg-blue-500 text-white px-2 py-1 rounded text-xs mb-2">
                    + Tambah Baris
                </button>

                <div class="mt-4 text-right">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded text-xs">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function formJSA(existing) {
    // ✅ PAKSA existing jadi array jika undefined/null/string kosong
    let data = Array.isArray(existing) ? existing : (existing ? JSON.parse(existing) : []);
    return {
        langkahKerja: data.length ? data : [{ langkah: '', bahaya: '', pengendalian: '' }],
        tambahRow() { this.langkahKerja.push({ langkah: '', bahaya: '', pengendalian: '' }); },
        hapusRow(index) { this.langkahKerja.splice(index, 1); },
        serializeLangkah() {
            document.getElementById('langkah_kerja').value = JSON.stringify(this.langkahKerja);
        }
    };
}
</script>