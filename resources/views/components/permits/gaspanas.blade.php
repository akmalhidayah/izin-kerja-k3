<!-- Bagian 1: Detail Pekerjaan -->
<div class="text-center">
        <h2 class="text-2xl font-bold uppercase">IZIN KERJA</h2>
        <h3 class="text-xl font-semibold text-gray-700">Pekerjaan dengan Material/Gas Panas ≥ 150°C</h3>
        <p class="text-sm mt-2 text-gray-600">
            Izin kerja ini harus diterbitkan untuk semua pekerjaan dengan paparan material/gas panas ≥ 150° Celcius seperti 
            <em>cleaning bunker</em>, perbaikan pada <em>riser duct</em>, saat kiln <em>running</em> dan lain-lain yang sejenis. 
            Izin ini tidak berlaku untuk pekerjaan rutin seperti merojok <em>poke hole cyclone</em>, dan lainnya.
            Pekerjaan tidak dapat dimulai hingga izin kerja ini diverifikasi oleh <em>Permit Verificator</em>,
            diterbitkan oleh <em>Permit Issuer</em>, disahkan oleh <em>Permit Authorizer</em>, dan 
            <em>major hazards & control</em> disosialisasikan oleh <em>Permit Receiver</em>.
        </p>
    </div>
<div class="border border-gray-800 rounded-md p-4 bg-white shadow space-y-4">
    <h3 class="bg-black text-white px-2 py-1 font-bold mt-4">1. Detail Pekerjaan</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
            <label class="font-semibold">Lokasi pekerjaan:</label>
            <input type="text" name="lokasi_pekerjaan" class="input w-full text-sm">
        </div>
        <div>
            <label class="font-semibold">Tanggal:</label>
            <input type="date" name="tanggal_pekerjaan" class="input w-full text-sm">
        </div>
    </div>

    <div>
        <label class="font-semibold">Uraian pekerjaan:</label>
        <textarea name="uraian_pekerjaan" class="textarea w-full text-sm"></textarea>
    </div>

    <div>
        <label class="font-semibold">Peralatan/perlengkapan yang akan digunakan pada pekerjaan:</label>
        <textarea name="peralatan_digunakan" class="textarea w-full text-sm"></textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
            <label class="font-semibold">Perkiraan jumlah pekerja yang akan terlibat dalam pekerjaan ini:</label>
            <input type="number" name="jumlah_pekerja" class="input w-full text-sm">
        </div>
        <div>
            <label class="font-semibold">Nomor gawat darurat yang harus dihubungi saat darurat:</label>
            <input type="text" name="nomor_darurat" class="input w-full text-sm">
        </div>
    </div>
</div>

<!-- Bagian 2: Daftar Pekerja dan Sketsa Pekerjaan -->
<div x-data="{ pekerja: [{}] }" class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        2. Daftar Pekerja dan Sketsa Pekerjaan 
        <span class="font-normal text-xs italic">(bisa dalam lampiran terpisah)</span>
    </h3>

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100 text-center">
            <tr>
                <th class="border px-2 py-1 w-1/2">Nama</th>
                <th class="border px-2 py-1 w-1/2">Paraf</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(item, index) in pekerja" :key="index">
                <tr>
                    <td class="border px-2 py-1">
                        <input type="text" :name="'pekerja[' + index + '][nama]'" x-model="item.nama" class="input w-full text-sm">
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <button 
                            type="button"
                            class="text-blue-600 underline text-xs"
                            @click="Alpine.store('signatureModal').openModal('Pekerja - ' + (index + 1))">
                            Tanda Tangan
                        </button>
                        <input type="hidden" :name="'pekerja[' + index + '][signature]'">
                    </td>
                </tr>
            </template>
        </tbody>
    </table>

    <div class="mt-3">
        <button type="button" @click="pekerja.push({})"
            class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">
            + Tambah Baris
        </button>
    </div>
</div>

<!-- Bagian 3: Persyaratan Kerja Aman -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">3. Persyaratan Kerja Aman</h3>

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-left">Persyaratan</th>
                <th class="border px-2 py-1 text-center w-12">Ya</th>
                <th class="border px-2 py-1 text-center w-12">N/A</th>
            </tr>
        </thead>
        <tbody>
            @php
                $syaratKerja = [
                    'Area kerja sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
                    'Area kerja sudah dibatasi dengan memasang barikade atau <em>safety line</em>.',
                    'Area kerja sudah diamankan dari potensi pekerja terpeleset/tersandung.',
                    'Area kerja di bawah sudah diamankan dengan barikade/safety line dan tersedia rambu peringatan tentang bahaya material/gas panas.',
                    'Tersedia jalur evakuasi yang aman dan semua pekerja sudah mengetahuinya.',
                    '<em>Job Safety Analysis/Safe Working Procedure</em> sudah tersedia dan semua pengendalian bahayanya sudah dilakukan.',
                    'Pekerja tidak bekerja sendirian, minimum oleh dua orang pekerja.',
                    'Semua pekerja yang terlibat terlatih, memahami bahaya, risiko dan pengendaliannya.',
                    'Pekerja memahami harus selalu berdiri di posisi arah berlawanan dari potensi semburan material/gas panas.',
                    'Pekerja memahami harus menjaga jarak aman dari potensi semburan material/gas panas.',
                    'Pekerjaan dilakukan secara bergantian dalam kurun waktu tertentu untuk mencegah <em>heat stress</em> yang bisa dialami pekerja.',
                    'Tim Rescue tersedia dan bisa segera datang melakukan pertolongan jika terjadi kondisi korban terkena paparan material/gas panas.',
                    'Peralatan/perlengkapan kerja yang akan digunakan sudah diperiksa dan dipastikan layak dan aman untuk digunakan.',
                    'Alat pelindung diri tahan panas yang akan dipakai sudah diperiksa dan dinyatakan layak dan aman untuk dipakai.',
                    'Pakaian pelindung tahan panas terbuat dari bahan aluminized atau minimum terbuat dari bahan tahan panas seperti Nomex IIIA.',
                    'Semua pekerja yang terlibat sudah menggunakan alat pelindung diri tahan panas yang sesuai, menutupi seluruh tubuh mulai dari kepala sampai ujung kaki.',
                    'Tersedia emergency shower yang bisa diakses dengan segera oleh pekerja jika terkena paparan material/gas panas.'
                ];
            @endphp

            @foreach ($syaratKerja as $i => $item)
                <tr>
                    <td class="border px-2 py-1">{!! $item !!}</td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="syarat[{{ $i }}]" value="ya">
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="syarat[{{ $i }}]" value="na">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Bagian 4: Rekomendasi Persyaratan Kerja Aman Tambahan -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        4. Rekomendasi Persyaratan Kerja Aman Tambahan dari 
        <em>Permit Verificator/Permit Issuer</em> 
        <span class="text-gray-300 text-xs">(jika ada)</span>
    </h3>

    <table class="table-auto w-full text-sm border mt-2">
        <tr>
            <td class="border px-2 py-2 align-top">
                <textarea name="rekomendasi_tambahan" class="w-full h-32 border rounded p-2 resize-none" placeholder="Tulis rekomendasi jika ada..."></textarea>
            </td>
            <td class="border text-center align-top px-4 py-2 w-24">
                <label class="block mb-1 font-medium">Ya</label>
                <input type="radio" name="rekomendasi_status" value="ya">
            </td>
        </tr>
    </table>
</div>

<!-- Bagian 5: Permohonan Izin Kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">5. Permohonan Izin Kerja</h3>

    <div class="p-2 border-t-0 border border-gray-300">
        <p class="text-sm italic font-semibold mb-2">Permit Requestor:</p>
        <p class="text-sm">
            Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk dapat melakukan pekerjaan ini.
        </p>
    </div>

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center w-1/4">Nama:</th>
                <th class="border px-2 py-1 text-center w-1/4">Tanda tangan:</th>
                <th class="border px-2 py-1 text-center w-1/4">Tanggal:</th>
                <th class="border px-2 py-1 text-center w-1/4">Jam:</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-2 text-center">
                    <input type="text" name="permit_requestor_name" class="input w-full text-center">
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
                    <input type="date" name="permit_requestor_date" class="input w-full text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_requestor_time" class="input w-full text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>
<!-- Bagian 6: Verifikasi Izin Kerja -->
<div x-data="{ diverifikasi: [{}] }" class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">6. Verifikasi Izin Kerja</h3>

    <div class="p-2 border-t-0 border border-gray-300">
        <p class="text-sm italic font-semibold mb-1">Hot Material/Gasses Permit Verificator:</p>
        <p class="text-sm">
            Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
            <strong>Berikut nama-nama pekerja yang diizinkan untuk melakukan pekerjaan:</strong>
        </p>
    </div>

    <!-- Input nama-nama -->
    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100 text-center">
            <tr>
                <th class="border px-2 py-1">Nama</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(row, index) in diverifikasi" :key="index">
                <tr>
                    <td class="border px-2 py-1">
                        <input type="text" :name="'diverifikasi[' + index + '][nama]'" x-model="row.nama" class="input w-full text-sm">
                    </td>
                </tr>
            </template>
        </tbody>
    </table>

    <div class="mt-3">
        <button type="button" @click="diverifikasi.push({})"
            class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">
            + Tambah Baris
        </button>
    </div>

    <!-- Tanda tangan -->
    <table class="table-auto w-full text-sm border mt-6">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center w-1/4">Nama:</th>
                <th class="border px-2 py-1 text-center w-1/4">Tanda Tangan:</th>
                <th class="border px-2 py-1 text-center w-1/4">Tanggal:</th>
                <th class="border px-2 py-1 text-center w-1/4">Jam:</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-2 text-center">
                    <input type="text" name="verificator_name" class="input w-full text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Hot Verificator')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_Hot Verificator">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="verificator_date" class="input w-full text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="verificator_time" class="input w-full text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Bagian 7: Penerbitan Izin Kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">7. Penerbitan Izin Kerja</h3>

    <!-- Pernyataan -->
    <div class="border border-t-0 border-gray-300 p-3">
        <p class="text-sm">
            <strong><em>Permit Issuer:</em></strong><br>
            Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan/atau rekomendasi persyaratan kerja aman tambahan dari <em>Permit Issuer</em> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
        </p>
    </div>

    <!-- Tabel tanda tangan -->
    <div class="overflow-x-auto mt-3">
        <table class="table-auto min-w-full text-sm border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-2 py-1 text-center">Nama:</th>
                    <th class="border px-2 py-1 text-center">Tanda Tangan:</th>
                    <th class="border px-2 py-1 text-center">Tanggal:</th>
                    <th class="border px-2 py-1 text-center">Jam:</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border px-2 py-2 text-center">
                        <input type="text" name="permit_issuer_name" class="input w-full text-center">
                    </td>
                    <td class="border px-2 py-2 text-center">
                        <button 
                            type="button"
                            @click="Alpine.store('signatureModal').openModal('Permit Issuer')"
                            class="text-blue-600 underline text-xs">
                            Tanda Tangan
                        </button>
                        <input type="hidden" name="signature_Permit Issuer">
                    </td>
                    <td class="border px-2 py-2 text-center">
                        <input type="date" name="permit_issuer_date" class="input w-full text-center">
                    </td>
                    <td class="border px-2 py-2 text-center">
                        <input type="time" name="permit_issuer_time" class="input w-full text-center">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
<!-- Baris waktu izin berlaku (versi kompak) -->
<div class="mt-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
        <div class="flex items-center gap-2">
            <label class="whitespace-nowrap font-medium">Dari Tanggal:</label>
            <input type="date" name="izin_berlaku_dari" class="input text-xs w-full">
            <label class="whitespace-nowrap font-medium">Jam:</label>
            <input type="time" name="izin_berlaku_jam_dari" class="input text-xs w-28">
        </div>
        <div class="flex items-center gap-2">
            <label class="whitespace-nowrap font-medium">Sampai Tanggal:</label>
            <input type="date" name="izin_berlaku_sampai" class="input text-xs w-full">
            <label class="whitespace-nowrap font-medium">Jam:</label>
            <input type="time" name="izin_berlaku_jam_sampai" class="input text-xs w-28">
        </div>
    </div>
</div>
</div>

<!-- Bagian 8: Pengesahan Izin Kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">8. Pengesahan Izin Kerja</h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="text-sm">
            <strong><em>Permit Authorizer:</em></strong><br>
            Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Issuer</em> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah menekankan apa saja <em>major hazards</em> dan pengendaliannya yang harus disosialisasikan oleh 
            <em>Permit Receiver</em> kepada seluruh pekerja terkait.
        </p>
    </div>

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center w-1/4">Nama:</th>
                <th class="border px-2 py-1 text-center w-1/4">Tanda Tangan:</th>
                <th class="border px-2 py-1 text-center w-1/4">Tanggal:</th>
                <th class="border px-2 py-1 text-center w-1/4">Jam:</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-2 text-center">
                    <input type="text" name="permit_authorizer_name" class="input w-full text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Permit Authorizer')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_Permit Authorizer">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="permit_authorizer_date" class="input w-full text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_authorizer_time" class="input w-full text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>


<!-- Bagian 9 : Pelaksanaan Pekerjaan -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">9. Pelaksanaan Pekerjaan</h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="text-sm">
            <strong><em>Permit Receiver:</em></strong><br>
            Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari Permit Issuer telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah mensosialisasikan apa saja major hazards dan pengendaliannya dari pekerjaan ini kepada seluruh pekerja terkait.
        </p>
    </div>

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center w-1/4">Nama:</th>
                <th class="border px-2 py-1 text-center w-1/4">Tanda Tangan:</th>
                <th class="border px-2 py-1 text-center w-1/4">Tanggal:</th>
                <th class="border px-2 py-1 text-center w-1/4">Jam:</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-2 text-center">
                    <input type="text" name="permit_authorizer_name" class="input w-full text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Permit Authorizer')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_Permit Authorizer">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="permit_authorizer_date" class="input w-full text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_authorizer_time" class="input w-full text-center">
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
<!-- Tombol Simpan dan Submit -->
<div class="flex justify-center gap-4 mt-8">
    <!-- Tombol Simpan Draft -->
    <button type="submit" name="action" value="save"
        class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-6 rounded shadow-md transition duration-200">
        💾 Simpan Draft
    </button>

    <!-- Tombol Submit Final -->
    <button type="submit" name="action" value="submit"
        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow-md transition duration-200">
        🚀 Submit Final
    </button>
</div>

