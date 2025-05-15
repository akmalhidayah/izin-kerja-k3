<div x-data="{
    bahaya: [''],
    apd: [''],
    kompetensi: ['']
}" class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-xs">
    <h3 class="font-bold italic text-center mb-2">Safe Working Procedure</h3>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-2">
        <div>
            <label class="block font-semibold mb-1">Nama SWP:</label>
            <input type="text" name="swp_nama" class="input w-full text-xs">
        </div>
        <div>
            <label class="block font-semibold mb-1">Lokasi:</label>
            <input type="text" name="swp_lokasi" class="input w-full text-xs">
        </div>
        <div>
            <label class="block font-semibold mb-1">Dibuat Tanggal:</label>
            <input type="date" name="swp_dibuat_tanggal" class="input w-full text-xs">
        </div>
        <div>
            <label class="block font-semibold mb-1">Tanggal Revisi Terakhir:</label>
            <input type="date" name="swp_revisi_terakhir" class="input w-full text-xs">
        </div>
    </div>

    <table class="table-auto w-full border text-xs">
        <thead class="bg-gray-100 text-center">
            <tr>
                <th class="border px-2 py-1">Bahaya atau risiko yang mungkin muncul</th>
                <th class="border px-2 py-1">Alat Pelindung Diri (APD) / Peralatan Kerja</th>
                <th class="border px-2 py-1">Persyaratan kompetensi & pelatihan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <!-- Bahaya -->
                <td class="border px-2 py-1 align-top">
                    <template x-for="(item, index) in bahaya" :key="index">
                        <div class="flex items-center mb-1">
                            <span class="mr-1">•</span>
                            <input type="text" :name="`bahaya[${index}]`" x-model="bahaya[index]" class="input w-full text-xs mr-1">
                            <button type="button" @click="bahaya.splice(index,1)" class="text-red-500 text-xs">Hapus</button>
                        </div>
                    </template>
                    <button type="button" @click="bahaya.push('')" class="text-blue-500 text-xs mt-1">+ Tambah Poin</button>
                </td>

                <!-- APD -->
                <td class="border px-2 py-1 align-top">
                    <template x-for="(item, index) in apd" :key="index">
                        <div class="flex items-center mb-1">
                            <span class="mr-1">•</span>
                            <input type="text" :name="`apd[${index}]`" x-model="apd[index]" class="input w-full text-xs mr-1">
                            <button type="button" @click="apd.splice(index,1)" class="text-red-500 text-xs">Hapus</button>
                        </div>
                    </template>
                    <button type="button" @click="apd.push('')" class="text-blue-500 text-xs mt-1">+ Tambah Poin</button>
                </td>

                <!-- Kompetensi -->
                <td class="border px-2 py-1 align-top">
                    <template x-for="(item, index) in kompetensi" :key="index">
                        <div class="flex items-center mb-1">
                            <span class="mr-1">•</span>
                            <input type="text" :name="`kompetensi[${index}]`" x-model="kompetensi[index]" class="input w-full text-xs mr-1">
                            <button type="button" @click="kompetensi.splice(index,1)" class="text-red-500 text-xs">Hapus</button>
                        </div>
                    </template>
                    <button type="button" @click="kompetensi.push('')" class="text-blue-500 text-xs mt-1">+ Tambah Poin</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div x-data="{
    prosedur: [''],
    referensi: ['']
}" class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-xs">

    <!-- Judul -->
    <table class="table-auto w-full border text-xs mb-2">
        <tr>
            <td colspan="3" class="border font-semibold text-center">Prosedur Kerja Aman:</td>
        </tr>
    </table>

    <!-- Prosedur Kerja Aman -->
    <table class="table-auto w-full border text-xs mb-2">
        <tr>
            <td class="border p-2" style="height: 200px;">
                <template x-for="(item, index) in prosedur" :key="index">
                    <div class="flex items-center mb-1">
                        <span class="mr-1">•</span>
                        <input type="text" :name="`prosedur[${index}]`" x-model="prosedur[index]" class="input w-full text-xs mr-1" placeholder="Tuliskan urutan langkah kerja beserta anjuran kerja amannya">
                        <button type="button" @click="prosedur.splice(index,1)" class="text-red-500 text-xs">Hapus</button>
                    </div>
                </template>
                <button type="button" @click="prosedur.push('')" class="text-blue-500 text-xs mt-1">+ Tambah Langkah</button>
            </td>
        </tr>
    </table>

    <!-- Referensi dan Catatan -->
    <table class="table-auto w-full border text-xs">
        <tr>
            <td class="border w-2/3 align-top p-2">
                <span class="font-semibold">Referensi (Guideline, Dokumen, Peraturan, Lain-lain):</span>
                <template x-for="(item, index) in referensi" :key="index">
                    <div class="flex items-center mb-1">
                        <span class="mr-1">•</span>
                        <input type="text" :name="`referensi[${index}]`" x-model="referensi[index]" class="input w-full text-xs mr-1" placeholder="Referensi">
                        <button type="button" @click="referensi.splice(index,1)" class="text-red-500 text-xs">Hapus</button>
                    </div>
                </template>
                <button type="button" @click="referensi.push('')" class="text-blue-500 text-xs mt-1">+ Tambah Referensi</button>
            </td>
            <td class="border w-1/3 p-2 text-xs">
                Prosedur kerja aman ini akan ditinjau ulang jika terjadi perubahan pada referensi, pekerjaan, peralatan atau material dan maksimum setiap 3 tahun
            </td>
        </tr>
    </table>

    <!-- Dibuat / Ditinjau / Disetujui -->
    <table class="table-auto w-full border text-xs mt-2">
        <tr class="text-center font-semibold">
            <td class="border px-2 py-1">Dibuat oleh:</td>
            <td class="border px-2 py-1">Ditinjau oleh:</td>
            <td class="border px-2 py-1">Disetujui oleh:</td>
        </tr>
        <tr class="text-center">
            <td class="border p-2">
                <input type="text" name="dibuat_oleh" class="input w-full text-xs mb-1" placeholder="Nama">
                <button type="button" @click="Alpine.store('signatureModal').openModal('Dibuat oleh')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
                <input type="hidden" name="signature_dibuat_oleh">
            </td>
            <td class="border p-2">
                <input type="text" name="ditinjau_oleh" class="input w-full text-xs mb-1" placeholder="Nama">
                <button type="button" @click="Alpine.store('signatureModal').openModal('Ditinjau oleh')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
                <input type="hidden" name="signature_ditinjau_oleh">
            </td>
            <td class="border p-2">
                <input type="text" name="disetujui_oleh" class="input w-full text-xs mb-1" placeholder="Nama">
                <button type="button" @click="Alpine.store('signatureModal').openModal('Disetujui oleh')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
                <input type="hidden" name="signature_disetujui_oleh">
            </td>
        </tr>
        <tr class="text-center font-semibold">
            <td class="border px-2 py-1">Team Leader</td>
            <td class="border px-2 py-1">Superintendent</td>
            <td class="border px-2 py-1">Manager</td>
        </tr>
    </table>
</div>
