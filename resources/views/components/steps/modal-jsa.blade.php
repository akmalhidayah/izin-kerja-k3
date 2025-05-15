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
<div x-data="formJSA()" x-init="console.log('✅ langkahKerja loaded:', langkahKerja)">
    {{-- MODAL --}}
    <div 
        x-show="activeModal === '{{ $id }}'" 
        x-cloak 
        class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center"
    >
        <div 
            class="bg-white p-6 rounded-lg shadow-md w-full max-w-5xl overflow-y-auto max-h-[90vh]"
            @click.away="activeModal = null"
        >
            <h2 class="text-lg font-semibold mb-4">Form Job Safety Analysis</h2>

            <form method="POST" action="{{ route('jsa.store') }}" @submit.prevent="serializeLangkah(); $el.submit()">
                @csrf

                {{-- HIDDEN INPUT --}}
                <input type="hidden" name="notification_id" value="{{ $notification->id ?? '' }}">
                <input type="hidden" name="dibuat_signature" id="dibuat_signature">
                <input type="hidden" name="disetujui_signature" id="disetujui_signature">
                <input type="hidden" name="diverifikasi_signature" id="diverifikasi_signature">
                <input type="hidden" name="langkah_kerja" id="langkah_kerja">

             {{-- HEADER --}}
<table class="table-auto w-full border text-xs mb-2">
    <tr>
        <td class="border px-2 py-1 font-semibold">Nama Perusahaan</td>
        <td class="border px-2 py-1" colspan="3">
            <input type="text" name="nama_perusahaan"
                class="input w-full text-xs"
                value="{{ old('nama_perusahaan') }}"
                required title="Nama Perusahaan tidak boleh kosong">
        </td>
    </tr>
    <tr>
        <td class="border px-2 py-1 font-semibold">Job Safety Analysis No</td>
        <td class="border px-2 py-1">
            <input type="text" name="no_jsa"
                class="input w-full text-xs"
                value="{{ old('no_jsa') }}"
                required title="Nomor JSA wajib diisi">
        </td>
        <td class="border px-2 py-1 font-semibold">Nama JSA</td>
        <td class="border px-2 py-1">
            <input type="text" name="nama_jsa"
                class="input w-full text-xs"
                value="{{ old('nama_jsa') }}"
                required title="Nama JSA wajib diisi">
        </td>
    </tr>
    <tr>
        <td class="border px-2 py-1 font-semibold">Departemen</td>
        <td class="border px-2 py-1">
            <input type="text" name="departemen"
                class="input w-full text-xs"
                value="{{ old('departemen') }}"
                required title="Departemen wajib diisi">
        </td>
        <td class="border px-2 py-1 font-semibold">Area Kerja</td>
        <td class="border px-2 py-1">
            <input type="text" name="area_kerja"
                class="input w-full text-xs"
                value="{{ old('area_kerja') }}"
                required title="Area Kerja wajib diisi">
        </td>
    </tr>
    <tr>
        <td class="border px-2 py-1 font-semibold">Tanggal</td>
        <td class="border px-2 py-1" colspan="3">
            <input type="date" name="tanggal"
                class="input w-full text-xs"
                value="{{ old('tanggal', date('Y-m-d')) }}"
                required title="Tanggal wajib dipilih">
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
                        <td class="border p-2 text-center">
                            <input type="text" name="dibuat_nama" class="input w-full text-xs mb-1" placeholder="Nama" value="{{ old('dibuat_nama') }}">
                            <button type="button" onclick="openSignPad('dibuat_signature')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
                        </td>
                        <td class="border p-2 text-center">
                            <input type="text" name="disetujui_nama" class="input w-full text-xs mb-1" placeholder="Nama" value="{{ old('disetujui_nama') }}">
                            <button type="button" onclick="openSignPad('disetujui_signature')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
                        </td>
                        <td class="border p-2 text-center">
                            <input type="text" name="diverifikasi_nama" class="input w-full text-xs mb-1" placeholder="Nama" value="{{ old('diverifikasi_nama') }}">
                            <button type="button" onclick="openSignPad('diverifikasi_signature')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
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
                                <td class="border px-2 py-1"><input type="text" x-model="row.langkah" class="input w-full text-xs"></td>
                                <td class="border px-2 py-1"><input type="text" x-model="row.bahaya" class="input w-full text-xs"></td>
                                <td class="border px-2 py-1"><input type="text" x-model="row.pengendalian" class="input w-full text-xs"></td>
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
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded text-xs">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function formJSA(existing = []) {
    return {
        langkahKerja: existing.length ? existing : [{ langkah: '', bahaya: '', pengendalian: '' }],
        tambahRow() { this.langkahKerja.push({ langkah: '', bahaya: '', pengendalian: '' }); },
        hapusRow(index) { this.langkahKerja.splice(index, 1); },
        serializeLangkah() {
            document.getElementById('langkah_kerja').value = JSON.stringify(this.langkahKerja);
        }
    }
}
</script>
