<!-- Bgain 1 detail pekerjaan -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        1. Detail Pekerjaan
    </h3>

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
                <input type="date" name="tanggal_pekerjaan" class="input w-full text-xs">
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
            <td class="border font-semibold px-2 py-1" colspan="2">Peralatan/perlengkapan yang akan digunakan pada pekerjaan:</td>
        </tr>
        <tr>
            <td class="border px-2 py-1" colspan="2">
                <textarea name="peralatan_digunakan" rows="2" class="input w-full text-xs"></textarea>
            </td>
        </tr>
        <tr>
            <td class="border font-semibold px-2 py-1" colspan="2">Perkiraan jumlah pekerja yang akan terlibat dalam pekerjaan ini:</td>
        </tr>
        <tr>
            <td class="border px-2 py-1" colspan="2">
                <input type="number" name="jumlah_pekerja" class="input w-full text-xs">
            </td>
        </tr>
        <tr>
            <td class="border font-semibold px-2 py-1" colspan="2">Nomor gawat darurat yang harus dihubungi saat darurat:</td>
        </tr>
        <tr>
            <td class="border px-2 py-1" colspan="2">
                <input type="text" name="nomor_darurat" class="input w-full text-xs">
            </td>
        </tr>
    </table>
</div>

<!-- bagian 2 daftar pekerja -->
<div x-data="{ pekerja: [{ nama: '', paraf: '' }] }" class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        2. Daftar Pekerja dan Sketsa Pekerjaan 
        <span class="italic text-xs font-normal">(bisa dalam lampiran terpisah)</span>
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
        <!-- Kolom kiri: Nama & Paraf -->
        <div>
            <table class="table-auto w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-1 text-center">Nama</th>
                        <th class="border px-2 py-1 text-center">Paraf</th>
                        <th class="border px-2 py-1 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, index) in pekerja" :key="index">
                        <tr>
                            <td class="border px-1 py-1">
                                <input type="text" :name="`nama_pekerja[${index}]`" x-model="row.nama" class="input w-full text-xs">
                            </td>
                            <td class="border px-1 py-1">
                                <input type="text" :name="`paraf_pekerja[${index}]`" x-model="row.paraf" class="input w-full text-xs">
                            </td>
                            <td class="border px-1 py-1 text-center">
                                <button type="button" @click="pekerja.splice(index, 1)" class="text-red-500 text-xs">Hapus</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <button type="button" @click="pekerja.push({ nama: '', paraf: '' })" class="mt-2 text-blue-600 text-xs">
                + Tambah Baris
            </button>
        </div>

        <!-- Kolom kanan: Upload Sketsa -->
        <div class="border border-gray-300 rounded-md p-4 bg-gray-50 flex flex-col justify-between">
            <label class="text-sm font-semibold mb-2">Upload Sketsa Pekerjaan (jika diperlukan):</label>
            <input type="file" name="sketsa_pekerjaan" class="input text-sm">
            <p class="text-xs italic text-gray-500 mt-2">* Lampirkan gambar sketsa bila diperlukan</p>
        </div>
    </div>
</div>

<!-- bagian 3 persyaratan kerja aman -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        3. Persyaratan Kerja Aman
        <span class="text-xs font-normal italic">(lingkari)</span>
    </h3>

    <table class="table-auto w-full border text-sm mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-left">Persyaratan</th>
                <th class="border px-2 py-1 text-center w-16">Ya</th>
                <th class="border px-2 py-1 text-center w-16">N/A</th>
            </tr>
        </thead>
        <tbody>
            @php
                $persyaratanKetinggian = [
                    'Area kerja sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
                    'Area kerja/area kerja dibawah sudah diamankan dari potensi jatuhan benda/material.',
                    'Area kerja sudah diamankan dari potensi pekerja terpleset/tersandung.',
                    '<em>Job Safety Analysis/Safe Working Procedure</em> sudah tersedia dan semua pengendaliannya sudah dilakukan.',
                    'Tepi terbuka/bukaan di lantai sudah dipasang pagar pengaman sementara/ditutup yang mencegah pekerja terjatuh.',
                    'Pekerja menggunakan <em>full body harness</em> yang difungsikan sebagai <em>fall restraint system</em>.',
                    'Semua pekerja yang terlibat terlatih, memahami risiko dan pengendaliannya untuk bekerja di ketinggian.',
                    'Semua pekerja terlibat sudah dinyatakan fit untuk bekerja di ketinggian.',
                    'Semua pekerja yang terlibat sudah menggunakan <em>full body harness</em> dan memahami cara penggunaannya dengan aman.',
                    'Semua pekerja yang terlibat memahami dimana akan mengaitkan <em>hook</em> FBH nya saat bekerja di ketinggian.',
                    'Semua pekerja terlibat memahami saat berpindah di ketinggian, <em>double hook</em> FBH nya dikaitkan secara bergantian.',
                    'Semua pekerja terlibat memahami teknik <em>three point contact</em> saat naik/turun tangga.',
                    '<em>Full body harness</em> yang akan dipakai sudah diperiksa dan dinyatakan layak dan aman untuk dipakai.',
                    'Peralatan/perlengkapan yang akan digunakan sudah diperiksa dan dipastikan layak dan aman untuk digunakan.',
                    'Peralatan kerja genggam tangan yang berisiko jatuh diberi tali pengikat untuk dikaitkan dengan aman saat di ketinggian.',
                    '<em>Man basket</em> sudah diperiksa, dipastikan layak dan aman untuk digunakan, pintu mengarah kedalam dan ada pengunci & <em>anchor point</em>.',
                    'Gondola sudah diperiksa, dipastikan layak dan aman untuk digunakan, mempunyai surat izin alat dan operator kompeten.',
                    '<em>Man basket</em>/gondola dilengkapi dengan <em>tag line</em> untuk mengendalikan pengangkatan.'
                ];
            @endphp

            @foreach ($persyaratanKetinggian as $index => $item)
                <tr>
                    <td class="border px-2 py-1">• {!! $item !!}</td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="kerja_aman_ketinggian[{{ $index }}]" value="ya">
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="kerja_aman_ketinggian[{{ $index }}]" value="na">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- bagian 4 rekomendasi persyaratan -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        4. Rekomendasi Persyaratan Kerja Aman Tambahan dari 
        <em>Permit Verificator/Permit Issuer</em> 
        <span class="text-xs font-normal italic">(jika ada)</span>
        <span class="float-right">(lingkari)</span>
    </h3>

    <table class="table-auto w-full border text-sm mt-2">
        <tbody>
            <tr>
                <td class="border px-2 py-6 w-full align-top">
                    <textarea name="rekomendasi_tambahan" rows="5" class="w-full border text-sm p-2 resize-none" placeholder="Tulis rekomendasi tambahan di sini jika ada..."></textarea>
                </td>
                <td class="border px-2 py-1 text-center align-top" style="width: 80px;">
                    <label class="block">Ya</label>
                    <input type="radio" name="rekomendasi_ada" value="ya">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- bagian 5 permohonan izin kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        5. Permohonan Izin Kerja
    </h3>

    <div class="border border-t-0 border-gray-300 p-3 bg-gray-50">
        <p class="italic font-semibold">Permit Requestor:</p>
        <p>
            Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk dapat melakukan pekerjaan ini.
        </p>
    </div>

    <!-- Tabel Nama, Tanda Tangan, Tanggal, Jam -->
    <table class="table-auto w-full border mt-3 text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center">Nama:</th>
                <th class="border px-2 py-1 text-center">Tanda tangan:</th>
                <th class="border px-2 py-1 text-center">Tanggal:</th>
                <th class="border px-2 py-1 text-center">Jam:</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-2 text-center">
                    <input type="text" name="permit_requestor_name" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Permit Requestor')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_permit_requestor">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="permit_requestor_date" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_requestor_time" class="input w-full text-xs text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>

 <!-- bagian 6 pervikasi izin kerja -->
 <div x-data="{ authorizedWorkers: [''] }" class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        6. Verifikasi Izin Kerja
    </h3>

    <div class="border border-t-0 border-gray-300 p-3 bg-gray-50">
        <p class="italic font-semibold">Working at Height Permit Verificator:</p>
        <p>
            Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk pekerjaan ini dapat dilakukan. 
            <strong>Berikut nama-nama pekerja yang diizinkan untuk bekerja di ketinggian:</strong>
        </p>
    </div>

    <!-- Daftar Nama Pekerja Dinamis -->
    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center">Nama Pekerja</th>
                <th class="border px-2 py-1 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(worker, index) in authorizedWorkers" :key="index">
                <tr>
                    <td class="border px-2 py-1">
                        <input type="text" :name="`authorized_worker[${index}]`" x-model="authorizedWorkers[index]" class="input w-full text-xs">
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <button type="button" @click="authorizedWorkers.splice(index, 1)" class="text-red-500 text-xs">Hapus</button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
    <button type="button" @click="authorizedWorkers.push('')" class="mt-2 text-blue-600 text-xs">
        + Tambah Pekerja
    </button>

    <!-- Tanda tangan Verifikator -->
    <table class="table-auto w-full border mt-4 text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center">Nama:</th>
                <th class="border px-2 py-1 text-center">Tanda tangan:</th>
                <th class="border px-2 py-1 text-center">Tanggal:</th>
                <th class="border px-2 py-1 text-center">Jam:</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-1 text-center">
                    <input type="text" name="verifikator_name" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-1 text-center">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Permit Verificator')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_verifikator">
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="date" name="verifikator_date" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="time" name="verifikator_time" class="input w-full text-xs text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- bagian 7 penerbitan izin kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        7. Penerbitan Izin Kerja
    </h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="text-sm italic font-semibold mb-2">Permit Issuer:</p>
        <p class="text-sm">
            Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
        </p>
    </div>

    <!-- Tabel Nama, Tanda Tangan, Tanggal, Jam -->
    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center">Nama:</th>
                <th class="border px-2 py-1 text-center">Tanda tangan:</th>
                <th class="border px-2 py-1 text-center">Tanggal:</th>
                <th class="border px-2 py-1 text-center">Jam:</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border text-center px-2 py-2">
                    <input type="text" name="permit_issuer_name" class="input w-full text-xs text-center">
                </td>
                <td class="border text-center px-2 py-2">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Permit Issuer')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_permit_issuer">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="date" name="permit_issuer_date" class="input w-full text-xs text-center">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="time" name="permit_issuer_time" class="input w-full text-xs text-center">
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Bagian berlaku dari tanggal - sampai tanggal (3 baris) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 text-sm">
        <div class="flex flex-col">
            <label class="font-semibold mb-1">Izin kerja ini berlaku dari tanggal:</label>
            <input type="date" name="izin_berlaku_dari" class="input text-xs">
        </div>
        <div class="flex flex-col">
            <label class="font-semibold mb-1">Jam mulai:</label>
            <input type="time" name="izin_berlaku_jam_dari" class="input text-xs">
        </div>
        <div class="flex flex-col">
            <label class="font-semibold mb-1">Sampai tanggal:</label>
            <input type="date" name="izin_berlaku_sampai" class="input text-xs">
        </div>
        <div class="flex flex-col">
            <label class="font-semibold mb-1">Jam selesai:</label>
            <input type="time" name="izin_berlaku_jam_sampai" class="input text-xs">
        </div>
    </div>
</div>

<!-- bagian 8 pengesahan izin kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        8. Pengesahan Izin Kerja
    </h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="text-sm italic font-semibold mb-2">Permit Authorizer:</p>
        <p class="text-sm">
            Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah menekankan apa saja <em>major hazards</em> dan pengendaliannya yang harus 
            disosialisasikan oleh <em>Permit Receiver</em> kepada seluruh pekerja terkait.
        </p>
    </div>

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center">Nama:</th>
                <th class="border px-2 py-1 text-center">Tanda tangan:</th>
                <th class="border px-2 py-1 text-center">Tanggal:</th>
                <th class="border px-2 py-1 text-center">Jam:</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border text-center px-2 py-2">
                    <input type="text" name="permit_authorizer_name" class="input w-full text-xs text-center">
                </td>
                <td class="border text-center px-2 py-2">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Permit Authorizer')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_permit_authorizer">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="date" name="permit_authorizer_date" class="input w-full text-xs text-center">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="time" name="permit_authorizer_time" class="input w-full text-xs text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- bagian 9 pelaksanaan pekerjaan -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        9. Pelaksanaan Pekerjaan
    </h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="text-sm italic font-semibold mb-2">Permit Receiver:</p>
        <p class="text-sm">
            Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah mensosialisasikan apa saja 
            <em>major hazards</em> dan pengendaliannya dari pekerjaan ini kepada seluruh pekerja terkait.
        </p>
    </div>

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center">Nama:</th>
                <th class="border px-2 py-1 text-center">Tanda tangan:</th>
                <th class="border px-2 py-1 text-center">Tanggal:</th>
                <th class="border px-2 py-1 text-center">Jam:</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border text-center px-2 py-2">
                    <input type="text" name="permit_receiver_name" class="input w-full text-xs text-center">
                </td>
                <td class="border text-center px-2 py-2">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Permit Receiver')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_permit_receiver">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="date" name="permit_receiver_date" class="input w-full text-xs text-center">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="time" name="permit_receiver_time" class="input w-full text-xs text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>
<!-- Bagian 10: Penutupan Izin Kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">10. Penutupan Izin Kerja</h3>

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-left">Item</th>
                <th class="border px-2 py-1 text-left">Keterangan</th>
                <th class="border px-2 py-1 text-center w-20">(O)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-1 font-semibold">Lock & Tag</td>
                <td class="border px-2 py-1">Semua <em>lock & tag</em> sudah dilepas</td>
                <td class="border px-2 py-1 text-center">
                    <label><input type="radio" name="close_lock_tag" value="ya"> Ya</label>
                    <label class="ml-2"><input type="radio" name="close_lock_tag" value="na"> N/A</label>
                </td>
            </tr>
            <tr>
                <td class="border px-2 py-1 font-semibold">Sampah & Peralatan Kerja</td>
                <td class="border px-2 py-1">Semua sampah sudah dibersihkan dan peralatan kerja sudah diamankan</td>
                <td class="border px-2 py-1 text-center">
                    <label><input type="radio" name="close_tools" value="ya"> Ya</label>
                    <label class="ml-2"><input type="radio" name="close_tools" value="na"> N/A</label>
                </td>
            </tr>
            <tr>
                <td class="border px-2 py-1 font-semibold">Machine Guarding</td>
                <td class="border px-2 py-1">Semua <em>machine guarding</em> sudah dipasang kembali</td>
                <td class="border px-2 py-1 text-center">
                    <label><input type="radio" name="close_guarding" value="ya"> Ya</label>
                    <label class="ml-2"><input type="radio" name="close_guarding" value="na"> N/A</label>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Tanda tangan -->
    <table class="table-auto w-full text-sm border mt-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center w-32">Tanggal:</th>
                <th class="border px-2 py-1 text-center w-32">Jam:</th>
                <th class="border px-2 py-1 text-center">Permit Requestor</th>
                <th class="border px-2 py-1 text-center">Permit Issuer</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border text-center px-2 py-2"><input type="date" name="close_date" class="input w-full text-center"></td>
                <td class="border text-center px-2 py-2"><input type="time" name="close_time" class="input w-full text-center"></td>
                <td class="border text-center px-2 py-2">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Penutup - Requestor')"
                        class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    <input type="hidden" name="signature_close_requestor">
                </td>
                <td class="border text-center px-2 py-2">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Penutup - Issuer')"
                        class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    <input type="hidden" name="signature_close_issuer">
                </td>
            </tr>
        </tbody>
    </table>
</div>
