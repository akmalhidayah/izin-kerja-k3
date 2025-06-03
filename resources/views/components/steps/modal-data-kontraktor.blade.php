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

@php
    $tenagaKerjaOld = old('tenaga_kerja')
        ? json_decode(old('tenaga_kerja'), true)
        : (isset($dataKontraktor) && $dataKontraktor->tenaga_kerja ? json_decode($dataKontraktor->tenaga_kerja, true) : []);
    $peralatanKerjaOld = old('peralatan_kerja')
        ? json_decode(old('peralatan_kerja'), true)
        : (isset($dataKontraktor) && $dataKontraktor->peralatan_kerja ? json_decode($dataKontraktor->peralatan_kerja, true) : []);
    $apdOld = old('apd')
        ? json_decode(old('apd'), true)
        : (isset($dataKontraktor) && $dataKontraktor->apd ? json_decode($dataKontraktor->apd, true) : []);
@endphp

<div
    x-data="formKontraktor({{ json_encode($tenagaKerjaOld) }}, {{ json_encode($peralatanKerjaOld) }}, {{ json_encode($apdOld) }})"
    x-init="console.log('✅ Form Kontraktor loaded', tenagaKerja, peralatanKerja, apd)"
>
    <div x-show="activeModal === '{{ $id }}'" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-5xl overflow-y-auto max-h-[90vh]" @click.away="activeModal = null">
            <h2 class="text-lg font-semibold mb-4">{{ $label }}</h2>
           <form method="POST" action="{{ route('izin-kerja.data-kontraktor', optional($notification)->id ?? 0) }}" @submit.prevent="serializeData(); $el.submit()">
    @csrf
    <input type="hidden" name="notification_id" value="{{ $notification?->id ?? '' }}">
    <input type="hidden" name="tenaga_kerja" id="tenaga_kerja">
    <input type="hidden" name="peralatan_kerja" id="peralatan_kerja">
    <input type="hidden" name="apd" id="apd">
    <input type="hidden" name="ttd_manager" id="ttd_manager" value="{{ old('ttd_manager', $dataKontraktor->ttd_manager ?? '') }}">
    <input type="hidden" name="ttd_perusahaan" id="ttd_perusahaan" value="{{ old('ttd_perusahaan', $dataKontraktor->ttd_perusahaan ?? '') }}">
    <input type="hidden" name="diverifikasi_signature" id="diverifikasi_signature" value="{{ old('diverifikasi_signature', $dataKontraktor->diverifikasi_signature ?? '') }}">


                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="text-sm font-semibold">Nama Perusahaan/Kontraktor</label>
                        <input type="text"
                            name="nama_perusahaan"
                            class="w-full text-sm border-gray-300 rounded p-2"
                            placeholder="PT. Contoh"
                            value="{{ old('nama_perusahaan', $notification?->user?->name ?? Auth::user()->name ?? '') }}">
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Jenis/Type Pekerjaan</label>
                        <input type="text"
                            name="jenis_pekerjaan"
                            class="w-full text-sm border-gray-300 rounded p-2"
                            placeholder="Deskripsi Pekerjaan"
                            value="{{ old('jenis_pekerjaan', $notification?->description ?? '') }}">
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Lokasi Kerja</label>
                        <input type="text"
                            name="lokasi_kerja"
                            class="w-full text-sm border-gray-300 rounded p-2"
                            placeholder="Lokasi Kerja"
                            value="{{ old('lokasi_kerja', $dataKontraktor->lokasi_kerja ?? '') }}">
                    </div>
                </div>

               <div class="grid grid-cols-2 gap-2 mb-4">
    <div>
        <label class="text-sm font-semibold">Jadwal Pekerjaan (Dari)</label>
        <input type="date"
            name="tanggal_mulai"
            class="w-full text-sm border-gray-300 rounded p-2"
            value="{{ old('tanggal_mulai', isset($dataKontraktor) && $dataKontraktor->tanggal_mulai ? \Carbon\Carbon::parse($dataKontraktor->tanggal_mulai)->format('Y-m-d') : '') }}">
    </div>
    <div>
        <label class="text-sm font-semibold">(Sampai)</label>
        <input type="date"
            name="tanggal_selesai"
            class="w-full text-sm border-gray-300 rounded p-2"
            value="{{ old('tanggal_selesai', isset($dataKontraktor) && $dataKontraktor->tanggal_selesai ? \Carbon\Carbon::parse($dataKontraktor->tanggal_selesai)->format('Y-m-d') : '') }}">
    </div>
</div>

                                <div class="mb-4">
                    <label class="text-sm font-semibold">No. OP/SPK/Notifikasi</label>
                    <input type="text"
                        name="nomor_notifikasi"
                        class="w-full text-sm border-gray-300 rounded p-2"
                        placeholder="No. Notifikasi"
                        value="{{ old('nomor_notifikasi', $notification?->number ?? '') }}"
                        readonly>
                </div>
{{-- Tenaga Kerja (Dinamis) --}}
                <h3 class="text-sm font-semibold mt-6 mb-2">1. Tenaga Kerja</h3>
                <table class="w-full text-xs border border-collapse mb-2">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-1">Nama Pekerja</th>
                            <th class="border p-1">Jumlah</th>
                            <th class="border p-1">Satuan</th>
                            <th class="border p-1 w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in tenagaKerja" :key="index">
                            <tr>
                                <td class="border p-1">
                                    <input type="text" x-model="item.nama" class="w-full text-xs border-gray-300 rounded p-1">
                                </td>
                                <td class="border p-1">
                                    <input type="number" x-model="item.jumlah" class="w-16 text-xs border-gray-300 rounded p-1" min="0">
                                </td>
                                <td class="border p-1">
                                    <input type="text" x-model="item.satuan" class="w-20 text-xs border-gray-300 rounded p-1" placeholder="Orang / Shift">
                                </td>
                                <td class="border p-1 text-center">
                                    <button type="button" @click="hapusTenagaKerja(index)" class="text-red-500 text-xs">Hapus</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <button type="button" @click="tambahTenagaKerja()" class="bg-blue-500 text-white px-2 py-1 rounded text-xs mb-2">+ Tambah Tenaga Kerja</button>
                {{-- Peralatan Kerja (Dinamis) --}}
                <h3 class="text-sm font-semibold mt-6 mb-2">2. Peralatan Kerja</h3>
                <table class="w-full text-xs border border-collapse mb-2">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-1">Nama Peralatan</th>
                            <th class="border p-1">Jumlah</th>
                            <th class="border p-1">Satuan</th>
                            <th class="border p-1 w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in peralatanKerja" :key="index">
                            <tr>
                                <td class="border p-1">
                                    <input type="text" x-model="item.nama" class="w-full text-xs border-gray-300 rounded p-1">
                                </td>
                                <td class="border p-1">
                                    <input type="number" x-model="item.jumlah" class="w-16 text-xs border-gray-300 rounded p-1" min="0">
                                </td>
                                <td class="border p-1">
                                    <input type="text" x-model="item.satuan" class="w-20 text-xs border-gray-300 rounded p-1" placeholder="Ea / Roll / Set">
                                </td>
                                <td class="border p-1 text-center">
                                    <button type="button" @click="hapusPeralatan(index)" class="text-red-500 text-xs">Hapus</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <button type="button" @click="tambahPeralatan()" class="bg-blue-500 text-white px-2 py-1 rounded text-xs mb-2">+ Tambah Peralatan</button>

                {{-- APD (Dinamis) --}}
                <h3 class="text-sm font-semibold mt-6 mb-2">3. Peralatan Kerja (APD)</h3>
                <table class="w-full text-xs border border-collapse mb-2">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-1">Nama APD</th>
                            <th class="border p-1">Jumlah</th>
                            <th class="border p-1">Satuan</th>
                            <th class="border p-1 w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in apd" :key="index">
                            <tr>
                                <td class="border p-1">
                                    <input type="text" x-model="item.nama" class="w-full text-xs border-gray-300 rounded p-1">
                                </td>
                                <td class="border p-1">
                                    <input type="number" x-model="item.jumlah" class="w-16 text-xs border-gray-300 rounded p-1" min="0">
                                </td>
                                <td class="border p-1">
                                    <input type="text" x-model="item.satuan" class="w-20 text-xs border-gray-300 rounded p-1" placeholder="Psg / Ea / Roll">
                                </td>
                                <td class="border p-1 text-center">
                                    <button type="button" @click="hapusApd(index)" class="text-red-500 text-xs">Hapus</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <button type="button" @click="tambahApd()" class="bg-blue-500 text-white px-2 py-1 rounded text-xs mb-2">+ Tambah APD</button>

                {{-- Tanda Tangan --}}
                <h3 class="text-sm font-semibold mt-6 mb-2">Tanda Tangan</h3>
                <table class="table-auto w-full border text-xs mb-2">
                    <tr>
                        <td colspan="2" class="border px-2 py-1 text-center font-semibold">Mengetahui</td>
                    </tr>
                 <tr>
    <td class="border px-2 py-6 text-center align-top" style="width: 50%;">
        Manager K3 Plant Site <br>
        <button type="button" onclick="openSignPad('ttd_manager')" class="text-blue-600 underline text-xs mt-2">Tanda Tangan</button>
        @if(!empty($dataKontraktor->ttd_manager))
            <div class="flex justify-center mt-2">
                <img src="{{ asset($dataKontraktor->ttd_manager) }}" class="h-12" alt="TTD Manager">
            </div>
        @endif
    </td>


   <td class="border px-2 py-6 text-center align-top">
    Perusahaan/Kontraktor <br>
    <button type="button" onclick="openSignPad('ttd_perusahaan')" class="text-blue-600 underline text-xs mt-2">Tanda Tangan</button>
    @if(!empty($dataKontraktor->ttd_perusahaan))
        <div class="flex justify-center mt-2">
            <img src="{{ asset($dataKontraktor->ttd_perusahaan) }}" class="h-12" alt="TTD Perusahaan">
        </div>
    @endif
</td>

</tr>

                    <tr>
                        <td class="border px-2 py-1 text-center font-semibold">
                            <input type="text" name="manager_nama" class="w-full text-xs border-gray-300 rounded p-1 mt-2" placeholder="Sjarifuddin Said" value="{{ old('manager_nama', $dataKontraktor->manager_nama ?? '') }}">
                        </td>
                        <td class="border px-2 py-1 text-center font-semibold">
                            <input type="text" name="perusahaan_nama" class="w-full text-xs border-gray-300 rounded p-1 mt-2" placeholder="Nama Perusahaan/Kontraktor" value="{{ old('perusahaan_nama', $dataKontraktor->perusahaan_nama ?? '') }}">
                        </td>
                    </tr>
                    <tr>
                        <td class="border px-2 py-1 text-center" colspan="2">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-xs">Paraf Diverifikasi oleh</span>
                                <input type="text" name="diverifikasi_nama" class="text-xs border-gray-300 rounded p-1 w-1/3 ml-2" placeholder="Nama" value="{{ old('diverifikasi_nama', $dataKontraktor->diverifikasi_nama ?? '') }}">
                                <button type="button" onclick="openSignPad('diverifikasi_signature')" class="text-blue-600 underline text-xs ml-2">Tanda Tangan</button>
                                @if(!empty($dataKontraktor->diverifikasi_signature))
                                    <img src="{{ asset($dataKontraktor->diverifikasi_signature) }}" class="h-10 ml-3 inline-block" alt="Paraf Verifikasi">
                                @endif
                            </div>
                        </td>
                    </tr>
                </table>

                <div class="flex justify-end mt-6">
                    <button type="button" @click="activeModal = null" class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600 mr-2">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function formKontraktor(tenagaKerjaInit = [], peralatanKerjaInit = [], apdInit = []) {
    return {
        tenagaKerja: tenagaKerjaInit && tenagaKerjaInit.length ? tenagaKerjaInit : [],
        peralatanKerja: peralatanKerjaInit && peralatanKerjaInit.length ? peralatanKerjaInit : [],
        apd: apdInit && apdInit.length ? apdInit : [],
        tambahTenagaKerja() { this.tenagaKerja.push({ nama: '', jumlah: '', satuan: '' }); },
        hapusTenagaKerja(index) { this.tenagaKerja.splice(index, 1); },
        tambahPeralatan() { this.peralatanKerja.push({ nama: '', jumlah: '', satuan: '' }); },
        hapusPeralatan(index) { this.peralatanKerja.splice(index, 1); },
        tambahApd() { this.apd.push({ nama: '', jumlah: '', satuan: '' }); },
        hapusApd(index) { this.apd.splice(index, 1); },
        serializeData() {
            document.getElementById('tenaga_kerja').value = JSON.stringify(this.tenagaKerja);
            document.getElementById('peralatan_kerja').value = JSON.stringify(this.peralatanKerja);
            document.getElementById('apd').value = JSON.stringify(this.apd);
        }
    }
}
</script>
