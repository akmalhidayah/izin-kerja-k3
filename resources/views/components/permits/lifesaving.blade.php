<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <!-- Header -->
    <div class="flex justify-between items-center mb-2">
        <div class="text-center">
            <h2 class="text-lg font-bold">LIFE SAVING TALK</h2>
            <p class="text-xs">Formulir ini digunakan pengawas pekerja untuk mensosialisasikan <em>major hazards</em> dan pengendaliannya ke seluruh pekerja terkait sebelum pekerjaan dengan izin kerja dimulai.</p>
        </div>
    </div>

    <!-- Detail Pekerjaan -->
    <h3 class="font-bold bg-black text-white px-2 py-1">1. Detail Pekerjaan</h3>
    <table class="table-auto w-full border text-sm">
        <tr>
            <td class="border font-semibold px-2 py-1 w-1/2">Lokasi pekerjaan:</td>
            <td class="border px-2 py-1 w-1/2">
                <input type="text" name="lokasi_pekerjaan" class="input w-full text-xs">
            </td>
        </tr>
        <tr>
            <td class="border font-semibold px-2 py-1">Tanggal:</td>
            <td class="border px-2 py-1">
                <input type="date" name="tanggal" class="input w-full text-xs">
            </td>
        </tr>
        <tr>
            <td class="border font-semibold px-2 py-1" colspan="2">Uraian pekerjaan:</td>
        </tr>
        <tr>
            <td class="border px-2 py-1" colspan="2">
                <textarea name="uraian_pekerjaan" rows="3" class="input w-full text-xs"></textarea>
            </td>
        </tr>
        <tr>
            <td class="border px-2 py-1 font-semibold italic">Workers Supervisor:</td>
            <td class="border px-2 py-1 italic">Job Supervisor:</td>
        </tr>
        <tr>
            <td class="border px-2 py-1">
                <input type="text" name="workers_supervisor" class="input w-full text-xs">
            </td>
            <td class="border px-2 py-1">
                <input type="text" name="job_supervisor" class="input w-full text-xs">
            </td>
        </tr>
    </table>
</div>

<div x-data="{ hazards: [{ major: '', control: '', pic: '' }] }" class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-xs">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        2. Bahaya Utama dan Pengendaliannya, Sketsa Pekerjaan
    </h3>

    <table class="table-auto w-full border text-xs mt-2">
        <thead class="bg-gray-100 text-center">
            <tr>
                <th class="border px-2 py-1">Major Hazards</th>
                <th class="border px-2 py-1">Controls</th>
                <th class="border px-2 py-1">PIC</th>
                <th class="border px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(hazard, index) in hazards" :key="index">
                <tr>
                    <td class="border px-2 py-1">
                        <input type="text" :name="`major_hazards[${index}]`" x-model="hazard.major" class="input w-full text-xs">
                    </td>
                    <td class="border px-2 py-1">
                        <input type="text" :name="`controls[${index}]`" x-model="hazard.control" class="input w-full text-xs">
                    </td>
                    <td class="border px-2 py-1">
                        <input type="text" :name="`pic[${index}]`" x-model="hazard.pic" class="input w-full text-xs">
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <button type="button" @click="hazards.splice(index, 1)" class="text-red-500 text-xs">Hapus</button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
    <button type="button" @click="hazards.push({ major: '', control: '', pic: '' })" class="mt-2 text-blue-600 text-xs">
        + Tambah Baris
    </button>

    <!-- Upload Sketsa -->
    <div class="mt-4">
        <label class="block font-semibold mb-1">Upload Sketsa Pekerjaan (jika diperlukan):</label>
        <input type="file" name="sketsa_pekerjaan" class="input w-full text-xs" accept="image/*">
        <p class="text-xs italic text-gray-500 mt-1">* Lampirkan gambar sketsa jika ada</p>
    </div>
</div>

<!-- Bagian 3 -->
<div x-data="{ pekerja: [{ nama: '', signature: '' }] }" class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-xs">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        3. Daftar Pekerja yang Terlibat
    </h3>

    <table class="table-auto w-full border text-xs mt-2">
        <thead class="bg-gray-100 text-center">
            <tr>
                <th class="border px-2 py-1">Nama</th>
                <th class="border px-2 py-1">Paraf / Tanda Tangan</th>
                <th class="border px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(row, index) in pekerja" :key="index">
                <tr>
                    <td class="border px-2 py-1">
                        <input type="text" :name="`pekerja_nama[${index}]`" x-model="row.nama" class="input w-full text-xs">
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <button 
                            type="button"
                            @click="Alpine.store('signatureModal').openModal(`Pekerja ${index + 1}`)"
                            class="text-blue-600 underline text-xs">
                            Tanda Tangan
                        </button>
                        <input type="hidden" :name="`pekerja_signature[${index}]`" x-model="row.signature">
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <button type="button" @click="pekerja.splice(index, 1)" class="text-red-500 text-xs">Hapus</button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>

    <button type="button" @click="pekerja.push({ nama: '', signature: '' })"
        class="mt-2 text-blue-600 text-xs">
        + Tambah Baris
    </button>
</div>

<div x-data="{ hadir: [{ nama: '', signature: '', tanggal: '', waktu: '', catatan: '' }] }" class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-xs">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        4. Daftar Hadir Pengawasan Pekerjaan
    </h3>

    <table class="table-auto w-full border text-xs mt-2">
        <thead class="bg-gray-100 text-center">
            <tr>
                <th class="border px-2 py-1">Nama</th>
                <th class="border px-2 py-1">Paraf / Tanda Tangan</th>
                <th class="border px-2 py-1">Tanggal</th>
                <th class="border px-2 py-1">Waktu</th>
                <th class="border px-2 py-1">Catatan</th>
                <th class="border px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(row, index) in hadir" :key="index">
                <tr>
                    <td class="border px-2 py-1">
                        <input type="text" :name="`hadir_nama[${index}]`" x-model="row.nama" class="input w-full text-xs">
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <button 
                            type="button"
                            @click="Alpine.store('signatureModal').openModal(`Hadir ${index + 1}`)"
                            class="text-blue-600 underline text-xs">
                            Tanda Tangan
                        </button>
                        <input type="hidden" :name="`hadir_signature[${index}]`" x-model="row.signature">
                    </td>
                    <td class="border px-2 py-1">
                        <input type="date" :name="`hadir_tanggal[${index}]`" x-model="row.tanggal" class="input w-full text-xs">
                    </td>
                    <td class="border px-2 py-1">
                        <input type="time" :name="`hadir_waktu[${index}]`" x-model="row.waktu" class="input w-full text-xs">
                    </td>
                    <td class="border px-2 py-1">
                        <input type="text" :name="`hadir_catatan[${index}]`" x-model="row.catatan" class="input w-full text-xs">
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <button type="button" @click="hadir.splice(index, 1)" class="text-red-500 text-xs">Hapus</button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>

    <button type="button" @click="hadir.push({ nama: '', signature: '', tanggal: '', waktu: '', catatan: '' })"
        class="mt-2 text-blue-600 text-xs">
        + Tambah Baris
    </button>
</div>


