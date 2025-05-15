<!-- bagian 1 detail pekerjaan -->
<div class="text-center">
    <h2 class="text-2xl font-bold uppercase">IZIN KERJA MENGANGKAT BEBAN</h2>
    <p class="text-sm mt-2 text-gray-600">
        Izin kerja ini diberikan untuk semua pekerjaan mengangkat beban dengan pesawat angkat bergerak seperti <em>mobile crane</em> dan lainnya,
        pengangkatan diluar rutinitas dengan pesawat angkat tetap berenergi seperti <em>overhead/hoist crane</em>, <em>hoist winch</em> dan lain-lain, 
        pengangkatan tandem. Pekerjaan tidak bisa dimulai hingga izin kerja diverifikasi oleh <em>Permit Verificator</em>,
        diterbitkan oleh <em>Permit Issuer</em>, disahkan oleh <em>Permit Authorizer</em> dan <em>major hazards & control</em> disosialisasikan oleh <em>Permit Receiver</em>.
    </p>
</div>
<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 space-y-4">
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

    <div>
        <label class="font-semibold">Uraian pekerjaan:</label>
        <textarea name="uraian_pekerjaan" class="textarea w-full text-sm" rows="3"></textarea>
    </div>

    <div>
        <label class="font-semibold">Peralatan/perlengkapan yang akan digunakan pada pekerjaan:</label>
        <textarea name="peralatan_digunakan" class="textarea w-full text-sm" rows="2"></textarea>
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
<!-- bagian 2 dokumentasi persyaratan -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        2. Dokumentasi Persyaratan Pengangkatan Beban
        <span class="text-xs font-normal italic">(lingkari)</span>
    </h3>

    <table class="table-auto w-full border text-sm mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-left">Dokumentasi</th>
                <th class="border px-2 py-1 text-center w-20">Ya</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-1">
                    <span class="ml-1">• <em>Operator</em> pesawat angkat memiliki lisensi K3 yang masih berlaku (lampirkan).</span>
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="radio" name="dok_operator" value="ya">
                </td>
            </tr>
            <tr>
                <td class="border px-2 py-1">
                    <span class="ml-1">• Juru ikat (<em>Rigger</em>) memiliki lisensi K3 yang masih berlaku (lampirkan).</span>
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="radio" name="dok_rigger" value="ya">
                </td>
            </tr>
            <tr>
                <td class="border px-2 py-1">
                    <span class="ml-1">• Pesawat angkat memiliki sertifikat uji kelayakan yang masih berlaku (lampirkan).</span>
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="radio" name="dok_sertifikat" value="ya">
                </td>
            </tr>
            <tr>
                <td class="border px-2 py-1">
                    <span class="ml-1">• <em>Load chart</em> pengangkatan tersedia, di unit <em>control display/hardcopy</em>.</span>
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="radio" name="dok_loadchart" value="ya">
                </td>
            </tr>
            <tr>
                <td class="border px-2 py-1">
                    <span class="ml-1">• Rencana pengangkatan telah dibuat oleh <em>Rigger</em> dan <em>Operator</em> dan disetujui oleh <em>Lifting Permit Verificator</em> (bisa lampiran terpisah).</span>
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="radio" name="dok_rencana_pengangkatan" value="ya">
                </td>
            </tr>
            <tr>
                <td class="border px-2 py-1">
                    <span class="ml-1">• <em>Job Safety Analysis/Safe Working Procedure</em> sudah tersedia dan semua pengendalian bahayanya sudah dilakukan (lampirkan).</span>
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="radio" name="dok_jsa" value="ya">
                </td>
            </tr>
        </tbody>
    </table>
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
                $persyaratanKerjaAman = [
                    'Area pengangkatan sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
                    'Area pengangkatan sudah dibatasi dengan memasang barricade atau safety line dan rambu peringatan.',
                    'Area pengangkatan sudah diamankan dari potensi jatuhan benda/material.',
                    'Area pengangkatan sudah diamankan dari potensi pekerja terpleset/tersandung.',
                    'Area pengangkatan bebas/dalam jarak aman dari kabel listrik bertegangan tinggi.',
                    'Radius kerja pesawat angkat sudah diamankan.',
                    'Sudah tersedia standby person untuk mengamankan area pengangkatan dan memahami tanggung jawabnya.',
                    'Komunikasi antara Rigger dan Operator menggunakan radio komunikasi/handphone/sinyal tangan.',
                    'Operator dan Rigger sudah menentukan kode sinyal tangan sebelum pengangkatan.',
                    'Aba-aba pengangkatan dilakukan oleh Rigger yang memiliki lisensi K3 yang masih berlaku.',
                    'Helper pekerjaan pengangkatan kompeten terhadap pekerjaan yang dilakukan.',
                    'Alat pelindung diri yang akan dipakai sudah diperiksa dan dinyatakan layak untuk dipakai.',
                    'Semua pekerja pengangkatan sudah menggunakan alat pelindung diri yang sesuai.',
                    'Semua pekerja terkait memahami bahwa dilarang berada dibawah beban yang sedang diangkat.',
                    'Semua pekerja terkait memahami dilarang naik pada beban yang sedang diangkat.',
                    'Pekerja yang bekerja di dalam man basket, sudah dinyatakan fit untuk bekerja di ketinggian.',
                    'Pesawat angkat ditempatkan pada permukaan yang datar, keras dan rata.',
                    'Pesawat angkat yang akan digunakan sudah diperiksa ulang dan dipastikan layak pakai.',
                    'Semua outrigger telah dikeluarkan maksimal diatas bantalan kayu/besi.',
                    'Counterweight pesawat angkat dipasang sebelum pengangkatan dilakukan.',
                    'Pesawat angkat dilengkapi dengan limit switch dan berfungsi baik.',
                    'Block hook pesawat angkat memiliki safety latch dan berfungsi baik.',
                    'Sudah dilakukan load test yang sesuai dengan kapasitas angkat hoist winch.',
                    'Alat bantu angkat yang akan digunakan sudah diperiksa dan dipastikan layak untuk digunakan, tertera SWL.',
                    'Pasang tali pandu pengarah (tag line) pada beban yang akan diangkat.',
                    'Man basket sudah diperiksa, dipastikan layak dan aman untuk digunakan, pintu mengarah kedalam, ada pengunci pintu, ada anchor point untuk hook FBH, ada tag line.',
                    'Tertera informasi SWL dan jumlah maksimum orang yang bisa diangkat pada man basket, dan berwarna kontras.',
                    'Maksimum hanya 2 pekerja yang boleh diangkat dalam man basket, kecuali ditentukan lain oleh orang yang kompeten.',
                    'Remote control hoist/overhead crane area pengangkatan mempunyai wind direction yang jelas.'
                ];
            @endphp

            @foreach ($persyaratanKerjaAman as $index => $persyaratan)
                <tr>
                    <td class="border px-2 py-1">• {{ $persyaratan }}</td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="persyaratan_kerja_aman[{{ $index }}]" value="ya">
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="persyaratan_kerja_aman[{{ $index }}]" value="na">
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
        <span class="text-xs font-normal">(Jika ada)</span> 
        <span class="text-xs font-normal">(lingkari)</span>
    </h3>

    <table class="table-auto w-full border mt-3 text-sm">
        <tbody>
            <tr>
                <td class="border px-2 py-2 align-top">
                    <textarea name="rekomendasi_kerja_aman" class="textarea w-full h-32 resize-none" placeholder="Tulis rekomendasi tambahan jika ada..."></textarea>
                </td>
                <td class="border text-center align-top w-24">
                    <label class="block mb-1 font-medium">Ya</label>
                    <input type="radio" name="rekomendasi_status" value="ya">
                </td>
            </tr>
        </tbody>
    </table>
</div>
<!-- bagian 5 Permohonan izin kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">5. Permohonan Izin Kerja</h3>

    <div class="p-2 border-t-0 border border-gray-300">
        <p class="text-sm italic font-semibold mb-2">Permit Requestor:</p>
        <p class="text-sm">
            Saya menyatakan bahwa semua dokumentasi persyaratan pengangkatan beban telah dilengkapi, semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk dapat melakukan pekerjaan ini.
        </p>
    </div>

    <table class="table-auto w-full text-sm border mt-3">
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
                    <input type="text" name="permit_requestor_name" class="input w-full text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <button type="button"
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

<!-- bagian 6 verifikasi izin kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        6. Verifikasi Izin Kerja
    </h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="text-sm italic font-semibold mb-2">Lifting Permit Verificator:</p>
        <p class="text-sm">
            Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua dokumentasi persyaratan
            pengangkatan beban, persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman
            tambahan dari <em>Permit Verificator/Permit Issuer</em> telah dipenuhi serta saya telah menyetujui rencana
            pengangkatan untuk pekerjaan ini dapat dilakukan.
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
                    <input type="text" name="verificator_name" class="input w-full text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Lifting Permit Verificator')"
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

<!-- bagian 7 Penerbitan izin kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        7. Penerbitan Izin Kerja
    </h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="text-sm italic font-semibold mb-2">Permit Issuer:</p>
        <p class="text-sm">
            Saya menyatakan bahwa saya telah memeriksa area kerja, semua dokumentasi persyaratan pengangkatan beban telah dilengkapi, 
            semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
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
                    <input type="text" name="permit_issuer_name" class="input w-full text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Permit Issuer')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_permit_issuer">
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

    <!-- Baris masa berlaku izin kerja -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm mt-4">
        <div class="flex items-center gap-2">
            <label class="font-semibold">Izin kerja ini berlaku dari tanggal:</label>
            <input type="date" name="izin_berlaku_dari" class="input w-full text-xs">
            <label class="font-semibold">Jam:</label>
            <input type="time" name="izin_berlaku_jam_dari" class="input text-xs w-28">
        </div>
        <div class="flex items-center gap-2">
            <label class="font-semibold">sampai tanggal:</label>
            <input type="date" name="izin_berlaku_sampai" class="input w-full text-xs">
            <label class="font-semibold">Jam:</label>
            <input type="time" name="izin_berlaku_jam_sampai" class="input text-xs w-28">
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
            Saya menyatakan bahwa saya telah memeriksa area kerja, semua dokumentasi persyaratan pengangkatan beban telah dilengkapi, 
            semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah menekankan apa saja 
            <em>major hazards</em> dan pengendaliannya yang harus disosialisasikan oleh <em>Permit Receiver</em> kepada seluruh pekerja terkait.
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
                    <input type="hidden" name="signature_permit_authorizer">
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

<!-- bagian 9 Pelaksanaan pekerjaan -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        9. Pelaksanaan Pekerjaan
    </h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="text-sm italic font-semibold mb-2">Permit Receiver:</p>
        <p class="text-sm">
            Saya menyatakan bahwa semua dokumentasi persyaratan pengangkatan beban telah dilengkapi, 
            semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah 
            mensosialisasikan apa saja <em>major hazards</em> dan pengendaliannya dari pekerjaan ini kepada seluruh pekerja terkait.
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
                    <input type="text" name="permit_receiver_name" class="input w-full text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Permit Receiver')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_permit_receiver">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="permit_receiver_date" class="input w-full text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_receiver_time" class="input w-full text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- bagian 10 Penutupan izin kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        10. Penutupan Izin Kerja <span class="text-xs italic font-normal">(lingkari)</span>
    </h3>

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-left">Item</th>
                <th class="border px-2 py-1 text-left">Keterangan</th>
                <th class="border px-2 py-1 text-center w-16">Ya</th>
                <th class="border px-2 py-1 text-center w-16">N/A</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-1 font-semibold italic">Lock & Tag</td>
                <td class="border px-2 py-1">Semua <em>lock & tag</em> sudah dilepas</td>
                <td class="border px-2 py-1 text-center">
                    <input type="radio" name="close_lock_tag" value="ya">
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="radio" name="close_lock_tag" value="na">
                </td>
            </tr>
            <tr>
                <td class="border px-2 py-1 font-semibold italic">Sampah & Peralatan Kerja</td>
                <td class="border px-2 py-1">Semua sampah sudah dibersihkan dan peralatan kerja sudah diamankan</td>
                <td class="border px-2 py-1 text-center">
                    <input type="radio" name="close_tools" value="ya">
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="radio" name="close_tools" value="na">
                </td>
            </tr>
            <tr>
                <td class="border px-2 py-1 font-semibold italic">Machine Guarding</td>
                <td class="border px-2 py-1">Semua <em>machine guarding</em> sudah dipasang kembali</td>
                <td class="border px-2 py-1 text-center">
                    <input type="radio" name="close_guarding" value="ya">
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="radio" name="close_guarding" value="na">
                </td>
            </tr>
        </tbody>
    </table>

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
                <td class="border text-center px-2 py-2">
                    <input type="date" name="close_date" class="input w-full text-center">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="time" name="close_time" class="input w-full text-center">
                </td>
                <td class="border text-center px-2 py-2">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Penutupan - Requestor')"
                        class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    <input type="hidden" name="signature_close_requestor">
                </td>
                <td class="border text-center px-2 py-2">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Penutupan - Issuer')"
                        class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    <input type="hidden" name="signature_close_issuer">
                </td>
            </tr>
        </tbody>
    </table>
</div>
