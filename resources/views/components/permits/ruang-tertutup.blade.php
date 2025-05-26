<div class="text-center">
    <h2 class="text-2xl font-bold uppercase">IZIN KERJA</h2>
    <h3 class="text-xl font-semibold text-gray-700">Bekerja di Ruang Tertutup/Terbatas</h3>
    <p class="text-sm mt-2 text-gray-600">
        Izin kerja ini diterbitkan untuk semua pekerjaan yang dilakukan di ruang tertutup/terbatas baik rutin maupun non rutin. 
        Pekerjaan tidak bisa dimulai hingga izin kerja diverifikasi oleh <em>Permit Verificator</em>, 
        diterbitkan oleh <em>Permit Issuer</em>, disahkan oleh <em>Permit Authorizer</em> 
        dan <em>major hazards & control</em> disosialisasikan oleh <em>Permit Receiver</em>.
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
            <label class="font-semibold">Perkiraan jumlah pekerja yang akan terlibat:</label>
            <input type="number" name="jumlah_pekerja" class="input w-full text-sm">
        </div>
        <div>
            <label class="font-semibold">Nomor gawat darurat yang dapat dihubungi:</label>
            <input type="text" name="nomor_darurat" class="input w-full text-sm">
        </div>
    </div>
</div>
<div x-data="{
    isolasiListrik: [{}],
    isolasiNonListrik: [{}]
}" class="border border-gray-800 rounded-md p-4 bg-white shadow space-y-6 mt-6">

    <h3 class="font-bold bg-black text-white px-2 py-1">
        2. Titik Isolasi dan Penguncian jika Diperlukan 
        <span class="text-xs font-normal italic">(diisi oleh <strong>Isolation Officer</strong>, bisa melampirkan <em>checklist isolation points</em>)</span>
    </h3>

    <!-- Isolasi Energi Listrik -->
    <div>
        <h4 class="font-semibold mt-2">Isolasi Energi Listrik</h4>
        <table class="table-auto w-full text-sm border mt-2">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">Peralatan / Mesin</th>
                    <th class="border p-2">Nomor peralatan/mesin</th>
                    <th class="border p-2">Tempat Isolasi dan Penguncian</th>
                    <th class="border p-2">Locked, Tagged</th>
                    <th class="border p-2">Tested</th>
                    <th class="border p-2">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(item, index) in isolasiListrik" :key="index">
                    <tr>
                        <td class="border p-1"><input type="text" x-model="item.peralatan" class="input w-full"></td>
                        <td class="border p-1"><input type="text" x-model="item.nomor" class="input w-full"></td>
                        <td class="border p-1"><input type="text" x-model="item.tempat" class="input w-full"></td>
                        <td class="border p-1"><input type="text" x-model="item.locked" class="input w-full"></td>
                        <td class="border p-1"><input type="text" x-model="item.tested" class="input w-full"></td>
                        <td class="border p-1 text-center">
                            <button 
                                type="button"
                                @click="Alpine.store('signatureModal').openModal('Listrik - ' + (index + 1))"
                                class="text-blue-600 underline text-xs">
                                Tanda Tangan
                            </button>
                            <input type="hidden" :name="'isolasiListrik[' + index + '][signature]'">
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <button @click="isolasiListrik.push({})"
            class="mt-2 text-xs bg-blue-500 text-white px-3 py-1 rounded">
            + Tambah Baris
        </button>
    </div>

    <!-- Isolasi Energi Non Listrik -->
    <div>
        <h4 class="font-semibold mt-4">Isolasi Energi Non Listrik 
            <span class="text-xs italic font-normal">(Thermal, chemical, radiation, potential, gravitational, mechanical, dll)</span>
        </h4>
        <table class="table-auto w-full text-sm border mt-2">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">Peralatan / Sumber Energi</th>
                    <th class="border p-2">Jenis (Hose, Valve dll)</th>
                    <th class="border p-2">Tempat Isolasi dan Penguncian</th>
                    <th class="border p-2">Locked, Tagged</th>
                    <th class="border p-2">Tested</th>
                    <th class="border p-2">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(item, index) in isolasiNonListrik" :key="index">
                    <tr>
                        <td class="border p-1"><input type="text" x-model="item.peralatan" class="input w-full"></td>
                        <td class="border p-1"><input type="text" x-model="item.jenis" class="input w-full"></td>
                        <td class="border p-1"><input type="text" x-model="item.tempat" class="input w-full"></td>
                        <td class="border p-1"><input type="text" x-model="item.locked" class="input w-full"></td>
                        <td class="border p-1"><input type="text" x-model="item.tested" class="input w-full"></td>
                        <td class="border p-1 text-center">
                            <button 
                                type="button"
                                @click="Alpine.store('signatureModal').openModal('Non Listrik - ' + (index + 1))"
                                class="text-blue-600 underline text-xs">
                                Tanda Tangan
                            </button>
                            <input type="hidden" :name="'isolasiNonListrik[' + index + '][signature]'">
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <button @click="isolasiNonListrik.push({})"
            class="mt-2 text-xs bg-blue-500 text-white px-3 py-1 rounded">
            + Tambah Baris
        </button>
    </div>
</div>

<div x-data="{
    dataGas: {
        'O2 (19.5% - 23.5%)': [],
        'LEL (< 5%)': [],
        'CO (≤ 25ppm)': [],
        'H2S (≤ 1ppm)': [],
        'O3 (≤ 0.2ppm)': []
    },
    addRow(gas) {
        this.dataGas[gas].push({
            tgl: '',
            hasil: '',
            jam: '',
            sign: ''
        });
    }
}" class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto text-sm">

    <h3 class="font-bold bg-black text-white px-2 py-1">
        3. Pengukuran Berkala Kadar Gas di Udara 
        <span class="text-xs font-normal italic">(diisi oleh <strong>Permit Verificator</strong>, bisa dalam lampiran terpisah)</span>
    </h3>

    <template x-for="(rows, gas) in dataGas" :key="gas">
        <div class="mt-4">
            <h4 class="font-bold text-sm mb-1" x-text="gas"></h4>
            <table class="table-auto w-full text-xs border">
                <thead class="bg-gray-100 text-center">
                    <tr>
                        <th class="border px-2 py-1">Tanggal</th>
                        <th class="border px-2 py-1">Hasil</th>
                        <th class="border px-2 py-1">Jam</th>
                        <th class="border px-2 py-1">Tanda Tangan</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, index) in rows" :key="index">
                        <tr>
                            <td class="border px-2 py-1">
                                <input type="date" x-model="row.tgl" class="input w-full text-xs">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="text" x-model="row.hasil" class="input w-full text-xs">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="time" x-model="row.jam" class="input w-full text-xs">
                            </td>
                            <td class="border px-2 py-1 text-center">
                                <button type="button"
                                    @click="Alpine.store('signatureModal').openModal(gas + ' - ' + (index + 1))"
                                    class="text-blue-600 underline text-xs">TTD</button>
                                <input type="hidden" :name="'signature[' + gas + '][' + index + ']'">
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <button type="button" @click="addRow(gas)"
                class="mt-2 bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">
                + Tambah Baris
            </button>
        </div>
    </template>
</div>
@php
    $syaratRuangTertutup = [
        'Area kerja sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
        'Lubang/bukaan akses keluar masuk sudah tersedia, semua bukaan/ventilasi yang mampu dibuka sudah dibuka atau dipasang <em>exhaust fan</em> untuk sirkulasi udara, lubang/bukaan untuk evakuasi saat terjadi kondisi gawat darurat sudah ditentukan.',
        'Bukaan/akses gawat darurat tidak terhalang oleh sesuatu yang bisa menghalangi keperluan evakuasi.',
        'Dipasang <em>exhaust fan</em> untuk sirkulasi udara di dalam ruang tertutup/terbatas.',
        'Akumulasi material yang bisa menimpa/menimbun pekerja sudah dirontokkan/dijatuhkan.',
        'Area kerja sudah diamankan dari potensi jatuhan benda/material seperti memasang <em>protection roofing</em>.',
        'Semua peralatan dan APD sudah diperiksa dinyatakan layak, semua pekerja menggunakan APD yang sesuai.',
        '<em>Job Safety Analysis/Safe Working Procedure</em> sudah tersedia dan semua pengendalian bahayanya sudah dilakukan.',
        'Semua pekerja yang terlibat terlatih, memahami bahaya, risiko dan pengendaliannya untuk melakukan pekerjaan <em>confined space</em>.',
        'Semua pekerja terlibat sudah dinyatakan fit untuk bekerja di dalam ruang tertutup/terbatas.',
        'Semua peralatan dan permesinan sudah diisolasi, dikunci, diuji dan ditandai oleh <em>Isolation Officer</em>.',
        'Semua pekerja sudah memasang personal lock pada titik isolasi yang sudah ditentukan.',
        'Ada <em>standby person</em> yang mengetahui nomor gawat darurat dan memahami tanggung jawabnya seperti memantau kondisi area kerja, menjaga komunikasi dengan pekerja baik secara visual maupun verbal, mengawasi masuk/keluar orang dan lain-lain.',
        'Pencahayaan di dalam ruang tertutup/terbatas sudah mencukupi.',
        'Rencana penyelamatan dan peralatan pertolongan sudah tersedia untuk pekerjaan di dalam ruang terbatas/tertutup.',
        'Pekerjaan tertentu didalam silo pekerja menggunakan FBH yang terhubung dengan <em>anchorage point</em>.'
    ];
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto text-sm mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">4. Persyaratan Kerja Aman</h3>

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-left">Persyaratan</th>
                <th class="border px-2 py-1 text-center w-16">Ya</th>
                <th class="border px-2 py-1 text-center w-16">N/A</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($syaratRuangTertutup as $index => $syarat)
                <tr>
                    <td class="border px-2 py-1">{!! $syarat !!}</td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="syarat_ruang_tertutup[{{ $index }}]" value="ya">
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="syarat_ruang_tertutup[{{ $index }}]" value="na">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<!-- Bagian 5: Rekomendasi Persyaratan Kerja Aman Tambahan -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        5. Rekomendasi Persyaratan Kerja Aman Tambahan dari 
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

<!-- Bagian 6: Permohonan Izin Kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">6. Permohonan Izin Kerja</h3>

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
<!-- Bagian 7: Verifikasi Izin Kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">7. Verifikasi Izin Kerja</h3>

    <div class="p-2 border-t-0 border border-gray-300">
        <p class="text-sm italic font-semibold mb-2">Confined Space Permit Verificator:</p>
        <p class="text-sm">
            Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman
            yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
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
                    <input type="text" name="confined_verificator_name" class="input w-full text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Confined Space Verificator')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_confined_verificator">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="confined_verificator_date" class="input w-full text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="confined_verificator_time" class="input w-full text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Bagian 8: Penerbitan Izin Kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">8. Penerbitan Izin Kerja</h3>

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
                        <input type="hidden" name="signature_permit issuer">
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
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">9. Pengesahan Izin Kerja</h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="text-sm">
            <strong><em>Permit Authorizer:</em></strong><br>
            Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan
            dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya 
            sudah menekankan apa saja <em>major hazards</em> dan pengendaliannya yang harus disosialisasikan oleh 
            <em>Permit Receiver</em> kepada seluruh pekerja terkait.
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
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">10. Pelaksanaan Pekerjaan</h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="text-sm">
            <strong><em>Permit Receiver:</em></strong><br>
            Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja 
            aman tambahan dari <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk dapat melakukan pekerjaan ini serta 
            saya sudah mensosialisasikan apa saja <em>major hazards</em> dan pengendaliannya dari pekerjaan ini kepada seluruh pekerja terkait.
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
<div x-data="{
    pekerjaList: [{
        nama: '',
        perusahaan: '',
        tanggal: '',
        masuk: '',
        keluar: '',
        sign: ''
    }],
    addRow() {
        this.pekerjaList.push({
            nama: '',
            perusahaan: '',
            tanggal: '',
            masuk: '',
            keluar: '',
            sign: ''
        });
    }
}" class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">

    <h3 class="font-bold bg-black text-white px-2 py-1">
        11. Daftar Pekerja Masuk/Keluar Ruang Tertutup/Terbatas
        <span class="italic text-xs font-normal">(tanda tangan di akhir shift, bisa dengan lampiran terpisah)</span>
    </h3>

    <table class="table-auto w-full text-xs border mt-2 text-center">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1">Nama</th>
                <th class="border px-2 py-1">Perusahaan</th>
                <th class="border px-2 py-1">Tanggal</th>
                <th class="border px-2 py-1">Waktu Masuk</th>
                <th class="border px-2 py-1">Waktu Keluar</th>
                <th class="border px-2 py-1">Sign</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(pekerja, index) in pekerjaList" :key="index">
                <tr>
                    <td class="border px-1 py-1">
                        <input type="text" x-model="pekerja.nama" class="input w-full text-xs">
                    </td>
                    <td class="border px-1 py-1">
                        <input type="text" x-model="pekerja.perusahaan" class="input w-full text-xs">
                    </td>
                    <td class="border px-1 py-1">
                        <input type="date" x-model="pekerja.tanggal" class="input w-full text-xs">
                    </td>
                    <td class="border px-1 py-1">
                        <input type="time" x-model="pekerja.masuk" class="input w-full text-xs">
                    </td>
                    <td class="border px-1 py-1">
                        <input type="time" x-model="pekerja.keluar" class="input w-full text-xs">
                    </td>
                    <td class="border px-1 py-1 text-center">
                        <button type="button"
                            @click="Alpine.store('signatureModal').openModal('Pekerja - ' + (index + 1))"
                            class="text-blue-600 underline text-xs">TTD</button>
                        <input type="hidden" :name="'signature_pekerja[' + index + ']'">
                    </td>
                </tr>
            </template>
        </tbody>
    </table>

    <div class="mt-3 text-right">
        <button @click="addRow()" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-4 py-1 rounded shadow">
            + Tambah Baris Pekerja
        </button>
    </div>
</div>
<div x-data="{
    checklistLiveTesting: [
        'Peralatan/permesinan yang akan dinyalakan kembali (re-energize) sudah diidentifikasi.',
        'Semua peralatan kerja sudah dipindahkan/diamankan dari peralatan / permesinan yang akan dinyalakan kembali.',
        'Semua orang yang dekat dengan area kerja sudah diinformasikan akan adanya peralatan/permesinan yang akan dinyalakan.',
        'Orang-orang yang tidak berkepentingan harus berada di luar area.',
        'Machine guarding pada peralatan/permesinan yang tidak mempengaruhi proses live testing sudah dipasang kembali.',
        'Semua lock dan tag sudah dilepaskan.',
        'Standby person telah ditunjuk untuk memastikan bahwa tidak ada orang berada di sekitar area dimana terdapat peralatan mesin tanpa machine guarding.',
        'Peralatan/permesinan sudah dinyalakan kembali.',
        'Setelah live testing, isolasi & penguncian harus kembali dipasang apabila pekerjaan belum selesai.'
    ]
}" class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">

    <h3 class="font-bold bg-black text-white px-2 py-1">
        12. <em>Live Testing</em> 
        <span class="italic text-xs font-normal">(Jika ada dan diisi oleh <strong>Isolation Officer</strong>)</span>
    </h3>

    <p class="text-xs italic mt-1 mb-2 text-gray-600">
        Jika peralatan/permesinan harus dinyalakan kembali (live testing), hal-hal berikut harus dilengkapi untuk memastikan pekerjaan dan kondisi area aman. 
        Petugas Isolasi HIL harus memberi paraf untuk setiap tahap apabila sudah dilengkapi.
    </p>

    <table class="table-auto w-full border text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-left">Checklist</th>
                <th class="border px-2 py-1 w-20 text-center">(Centang)</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(item, index) in checklistLiveTesting" :key="index">
                <tr>
                    <td class="border px-2 py-1" x-text="item"></td>
                    <td class="border px-2 py-1 text-center">
                        <input type="checkbox" :name="'live_testing[' + index + ']'" value="ya">
                    </td>
                </tr>
            </template>
        </tbody>
    </table>

    <table class="table-auto w-full text-sm border mt-4">
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
                    <input type="text" name="live_testing_name" class="input w-full text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <button type="button"
                        @click="Alpine.store('signatureModal').openModal('Live Testing Officer')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="live_testing_signature">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="live_testing_date" class="input w-full text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="live_testing_time" class="input w-full text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Bagian 13: Penutupan Izin Kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">13. Penutupan Izin Kerja</h3>

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
