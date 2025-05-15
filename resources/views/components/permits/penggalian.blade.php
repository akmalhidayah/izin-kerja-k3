<!-- Izin Kerja Penggalian - Bagian 1: Detail Pekerjaan -->
<div class="text-center mb-4">
    <h2 class="text-2xl font-bold uppercase">IZIN KERJA PENGGALIAN</h2>
    <p class="text-sm mt-2 text-gray-600 leading-snug">
        Izin kerja ini diberikan untuk semua pekerjaan penggalian dengan kedalaman ≥ 300 mm, izin ini tidak berlaku untuk penggalian di area tambang aktif.
        Pekerjaan tidak bisa dimulai hingga izin kerja diverifikasi oleh <em>Permit Verificator</em>, diterbitkan oleh <em>Permit Issuer</em>, disahkan oleh 
        <em>Permit Authorizer</em>, dan <em>major hazards & control</em> disosialisasikan oleh <em>Permit Receiver</em>.
    </p>
</div>

<div class="border border-gray-800 rounded-md p-4 bg-white shadow space-y-4">
    <h3 class="bg-black text-white px-2 py-1 font-bold">1. Detail Pekerjaan</h3>

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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
            <label class="font-semibold">Uraian pekerjaan:</label>
            <textarea name="uraian_pekerjaan" rows="5" class="textarea w-full text-sm"></textarea>
        </div>
        <div>
    <label class="font-semibold">Sketsa penggalian: 
        <span class="text-gray-400 text-xs">(upload gambar jpg/png/pdf)</span>
    </label>
    <input type="file" 
           name="sketsa_penggalian" 
           accept=".jpg,.jpeg,.png,.pdf" 
           class="block w-full mt-1 text-sm text-gray-700 file:mr-4 file:py-1 file:px-3
                  file:rounded-md file:border-0 file:text-sm file:font-semibold
                  file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
</div>
    </div>

    <div>
        <label class="font-semibold">Peralatan/perlengkapan yang akan digunakan pada pekerjaan:</label>
        <textarea name="alat_penggalian" class="textarea w-full text-sm"></textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
            <label class="font-semibold">Perkiraan jumlah pekerja:</label>
            <input type="number" name="jumlah_pekerja" class="input w-full text-sm">
        </div>
        <div>
            <label class="font-semibold">Nomor darurat yang dapat dihubungi:</label>
            <input type="text" name="nomor_darurat" class="input w-full text-sm">
        </div>
    </div>
</div>

<!--  Bagian 2: Gambar Denaah -->
<div x-data="{ showUpload: false }" class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        2. Gambar/Denah Fasilitas Bawah Tanah yang Diperlukan 
        <span class="text-xs font-normal">(beri tanda centang)</span>
    </h3>

    <!-- Daftar checkbox -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-3 text-sm">
        <div class="space-y-1">
            <label><input type="checkbox" name="denah[]" value="kabel_listrik" class="mr-1"> Jalur kabel listrik</label><br>
            <label><input type="checkbox" name="denah[]" value="kabel_optik" class="mr-1"> Jalur kabel optik/telpon</label><br>
            <label><input type="checkbox" name="denah[]" value="pipa_gas" class="mr-1"> Jalur pipa gas</label><br>
            <label><input type="checkbox" name="denah[]" value="pipa_proses" class="mr-1"> Jalur pipa proses</label><br>
            <label><input type="checkbox" name="denah[]" value="cable_tunnel" class="mr-1"> Jalur <em>cable tunnel</em></label>
        </div>
        <div class="space-y-1">
            <label><input type="checkbox" name="denah[]" value="pipa_air_hydrant" class="mr-1"> Jalur pipa air <em>hydrant</em></label><br>
            <label><input type="checkbox" name="denah[]" value="pipa_air_utilitas" class="mr-1"> Jalur pipa air utilitas</label><br>
            <label><input type="checkbox" name="denah[]" value="kabel_instrumen" class="mr-1"> Jalur kabel instrumentasi</label><br>
            <label><input type="checkbox" name="denah[]" value="selokan_septic" class="mr-1"> Jalur selokan dan <em>septic tank</em></label><br>
            <label><input type="checkbox" name="denah[]" value="lainnya" class="mr-1"> Jalur lainnya ……………………</label>
        </div>
    </div>

    <!-- Radio dan upload -->
    <p class="mt-3 text-sm">Jika ya, lampirkan untuk melengkapi izin penggalian ini</p>

    <div class="flex items-center gap-4 mt-2">
        <label class="inline-flex items-center gap-1">
            <input type="radio" name="denah_status" value="ya" x-model="showUpload" :checked="true" @change="showUpload = true"> Ya
        </label>
        <label class="inline-flex items-center gap-1">
            <input type="radio" name="denah_status" value="na" x-model="showUpload" @change="showUpload = false"> N/A
        </label>
    </div>

    <!-- Upload section -->
    <div x-show="showUpload" x-transition class="mt-4">
        <label class="block text-sm font-semibold mb-1">Upload Denah (PDF atau Gambar)</label>
        <input type="file" name="file_denah" accept=".pdf, .jpg, .jpeg, .png" class="block w-full text-sm text-gray-700 border border-gray-300 rounded p-2">
        <p class="text-xs text-gray-500 mt-1">Format diperbolehkan: PDF, JPG, PNG</p>
    </div>
</div>

<!-- Bagian 3: Persyaratan Kerja Aman -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 overflow-x-auto">
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
                $items = [
                    'Area penggalian sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
                    'Area penggalian sudah dibatasi dengan memasang barikade atau <em>safety line</em>.',
                    'Area kerja sudah diamankan dari potensi jatuhan benda/material.',
                    'Area kerja sudah diamankan dari potensi pekerja terpeleset/tersandung.',
                    'Pekerja yang melakukan pekerjaan penggalian kompeten terhadap pekerjaan yang dilakukan.',
                    '<em>Job Safety Analysis/Safe Working Procedure</em> sudah tersedia dan semua pengendalian bahayanya sudah dilakukan.',
                    'Peralatan kerja yang akan digunakan sudah diperiksa dan dipastikan layak untuk digunakan.',
                    'Alat pelindung diri yang akan dipakai sudah diperiksa dan dinyatakan layak untuk dipakai.',
                    'Semua pekerja penggalian sudah menggunakan alat pelindung diri yang sesuai.',
                    'Tersedia jalur masuk/keluar galian yang aman, tersedia jembatan yang aman jika diperlukan.',
                    'Tersedia tangga naik/turun dengan penempatan dan kemiringan yang aman (75°) dan terikat dengan kuat pada bibir galian.',
                    'Semua pekerja penggalian memahami teknik <em>three point contact</em> saat naik/turun tangga.',
                    'Area yang akan digali aman/sudah diamankan dari fasilitas bawah tanah seperti jalur kabel listrik, kabel optik, kabel instrumentasi, kabel telepon, pipa gas, pipa air <em>utility</em>, pipa proses, pipa air <em>hydrant</em>, selokan, <em>septic tank</em>, <em>cable tunnel</em>.',
                    'Melakukan metoda <em>cutback</em> 30° untuk tanah keras dan 46° untuk tanah lunak/dipadatan.',
                    'Metoda penyanggaan (<em>shoring</em>) lebih dari 3 meter disetujui oleh professional engineer.',
                    'Penumpukan tanah galian minimum 2 meter dari bibir galian dan kemiringan kurang dari 45°.',
                    'Garis barikade dengan jarak 2 meter dari bibir galian dan lengkap dengan rambu peringatan?',
                    'Genangan air pada lubang galian dibuang/dipompa keluar dari lubang galian.',
                    'Dipasang exhaust fan pada lubang galian untuk sirkulasi udara yang baik.',
                    'Diterapkan <em>Protective support system</em> untuk penggalian yang akan dilakukan seperti <em>shoring/shielding/benching</em>.',
                    'Dilakukan pengukuran kadar gas pada lubang galian (hasil pengukuran bisa dicatatkan pada lembar terpisah).',
                    'Diperlukan isolasi dan penguncian sebelum penggalian dilakukan (ajukan Izin Kerja Umum).',
                ];
            @endphp

            @foreach ($items as $i => $item)
                <tr>
                    <td class="border px-2 py-1">{!! $item !!}</td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="syarat_penggalian[{{ $i }}]" value="ya">
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="syarat_penggalian[{{ $i }}]" value="na">
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
<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">6. Verifikasi Izin Kerja</h3>

    <div class="p-2 border-t-0 border border-gray-300">
        <p class="text-sm italic font-semibold mb-2">Digging Permit Verificator:</p>
        <p class="text-sm">
            Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman yang telah
            ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
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
                    <input type="text" name="verificator_name" class="input w-full text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Digging Verificator')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_verificator">
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
