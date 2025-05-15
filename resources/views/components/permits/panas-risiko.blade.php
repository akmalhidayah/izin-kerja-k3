<!-- bagian 1 detail pekerjaan -->
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
                $persyaratanKerjaPanas = [
                    'Area kerja sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
                    'Area kerja/area di bawah pekerjaan panas sudah dibatasi dengan barricade/safety line.',
                    'Area kerja sudah diamankan dari potensi jatuhan benda/material.',
                    'Area kerja sudah diamankan dari potensi pekerja terpleset/tersandung.',
                    'Area kerja sudah diamankan dari potensi pekerja terjatuh saat pekerjaan panas di ketinggian.',
                    '<em>Job Safety Analysis/Safe Working Procedure</em> sudah tersedia dan semua pengendalian bahayanya sudah dilakukan.',
                    'Semua pekerja yang terlibat terlatih, memahami bahaya, risiko dan pengendaliannya untuk melakukan pekerjaan panas.',
                    'Semua pekerja memahami bahwa dilarang merokok di area kerja panas.',
                    'Juru las memiliki sertifikat keahlian yang sesuai dan mengetahui memasang kabel grounding harus ke benda kerja bukan ke struktur lainnya untuk mencegah risiko tersebut.',
                    'Pekerja panas dilengkapi dengan <em>personal gas detector</em>.',
                    'Semua pekerja yang terlibat sudah menggunakan alat pelindung diri yang sesuai untuk pekerjaan panas.',
                    'Ada Fire Sentry yang bisa menggunakan APAR, mengerti prosedur gawat darurat, memahami tanggung jawabnya seperti monitor area pekerjaan, tidak meninggalkan lokasi pekerjaan, memadamkan api yang bisa menyebabkan kebakaran, menghubungi nomor gawat darurat jika api tidak bisa dipadamkan, tetap berada di area kerja melakukan pemeriksaan minimum sampai 30 menit setelah selesai pekerjaan panas.',
                    'Peralatan kerja panas yang akan digunakan sudah diperiksa dan dipastikan layak dan aman untuk digunakan.',
                    'Sistem pembumian (<em>grounding</em>) pada trafo las telah terpasang dan berfungsi baik.',
                    'Trafo las terhubung pada <em>welding point</em> yang dilengkapi dengan ELCB.',
                    '<em>Flashback arrestor</em> terpasang pada kedua ujung selang gas tekanan.',
                    'Alat pelindung diri kerja panas yang akan dipakai sudah diperiksa dan dinyatakan layak dan aman untuk dipakai.',
                    'Bahan mudah terbakar sudah dipindahkan/disingkirkan dari area kerja (&gt;10 meter)/dipisahkan dengan dinding tahan api.',
                    'Bahan mudah terbakar pada <em>pipeline/duct, fine coal bin</em>, tangki limbah cair (BBS tank) tangki penyimpanan IDO (<em>Industrial Diesel Oil</em>), <em>explosives storage</em> atau sejenisnya sudah dikosongkan.',
                    'APAR/sarana pemadam api lainnya sudah disediakan untuk pekerjaan panas di area kerja.',
                    'Selimut tahan api, perisai panas sudah disediakan untuk mencegah <em>spark</em>.',
                    'Tabung gas bertekanan diletakkan dalam posisi berdiri dan tetap pada struktur yang kuat serta dilindungi dari jatuhan material.',
                    'Tersedia pemantik api untuk menyalakan api gas bertekanan.',
                    'Pekerjaan panas di ruang tertutup bukan ventilasi/sirkulasi udara baik atau dipasang <em>exhaust fan</em> untuk sirkulasi udara.'
                ];
            @endphp

            @foreach ($persyaratanKerjaPanas as $index => $persyaratan)
                <tr>
                    <td class="border px-2 py-1">• {!! $persyaratan !!}</td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="persyaratan_kerja_panas[{{ $index }}]" value="ya">
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="persyaratan_kerja_panas[{{ $index }}]" value="na">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- bagian 4 rekomendasi persyaratan -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        4. Rekomendasi Persyaratan Kerja Aman Tambahan dari 
        <em>Permit Verificator/Permit Issuer</em>
        <span class="text-xs font-normal">(jika ada)</span>
        <span class="float-right italic text-xs">(lingkari)</span>
    </h3>

    <div class="border border-t-0 border-gray-300 h-24">
        <textarea name="rekomendasi_kerja_aman_tambahan" class="w-full h-full p-2 text-xs border-none resize-none" placeholder="Tulis jika ada rekomendasi tambahan..."></textarea>
    </div>

    <table class="table-auto w-full border mt-1">
        <tr>
            <td class="border px-2 py-1 text-right w-full text-sm">Ya</td>
            <td class="border px-2 py-1 text-center w-16">
                <input type="radio" name="rekomendasi_kerja_aman_setuju" value="ya">
            </td>
        </tr>
    </table>
</div>

<!-- bagian 5 permohonan izin kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        5. Permohonan Izin Kerja
    </h3>

    <div class="border border-t-0 border-gray-300 p-3 bg-gray-50">
        <p class="italic font-semibold">Permit Requestor:</p>
        <p class="text-sm mt-1">
            Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk dapat melakukan pekerjaan ini.
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
                <td class="border px-2 py-1 text-center">
                    <input type="text" name="requestor_name" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-1 text-center">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Permit Requestor')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_requestor">
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="date" name="requestor_date" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="time" name="requestor_time" class="input w-full text-xs text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- bagian 6 verfikasi izin kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        6. Verifikasi Izin Kerja
    </h3>

    <div class="border border-t-0 border-gray-300 p-3 bg-gray-50">
        <p class="italic font-semibold">High Risk Hot Work Permit Verificator:</p>
        <p class="text-sm mt-1">
            Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
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
                <td class="border px-2 py-1 text-center">
                    <input type="text" name="verificator_name" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-1 text-center">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('High Risk Verificator')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_verificator">
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="date" name="verificator_date" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="time" name="verificator_time" class="input w-full text-xs text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- bagian 7 penerbitan izin kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-xs">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        7. Penerbitan Izin Kerja 
        <span class="text-xs font-normal italic">(Tanda tangan General Manager jika diperlukan)</span>
    </h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="italic font-semibold mb-2">Permit Issuer & Senior Manager:</p>
        <p>
            Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
        </p>
    </div>

    <!-- Baris input tanda tangan 3 kolom -->
    <table class="table-auto w-full text-xs border mt-3 text-center">
        <thead class="bg-gray-100 font-semibold">
            <tr>
                <th class="border px-2 py-1 italic">Permit Issuer</th>
                <th class="border px-2 py-1 italic">Senior Manager</th>
                <th class="border px-2 py-1 italic">General Manager</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-2">
                    <input type="text" name="permit_issuer_name" class="input w-full text-center mb-1 text-xs" placeholder="Nama">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Permit Issuer')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="permit_issuer_signature">
                </td>
                <td class="border px-2 py-2">
                    <input type="text" name="senior_manager_name" class="input w-full text-center mb-1 text-xs" placeholder="Nama">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Senior Manager')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="senior_manager_signature">
                </td>
                <td class="border px-2 py-2">
                    <input type="text" name="general_manager_name" class="input w-full text-center mb-1 text-xs" placeholder="Nama">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('General Manager')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="general_manager_signature">
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Baris tanggal berlaku -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-4 text-xs">
        <div>
            <label class="block font-semibold mb-1">Dari tanggal:</label>
            <input type="date" name="izin_berlaku_dari" class="input w-full text-xs">
        </div>
        <div>
            <label class="block font-semibold mb-1">Jam mulai:</label>
            <input type="time" name="izin_berlaku_jam_dari" class="input w-full text-xs">
        </div>
        <div>
            <label class="block font-semibold mb-1">Sampai tanggal:</label>
            <input type="date" name="izin_berlaku_sampai" class="input w-full text-xs">
        </div>
        <div>
            <label class="block font-semibold mb-1">Jam selesai:</label>
            <input type="time" name="izin_berlaku_jam_sampai" class="input w-full text-xs">
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
            Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah menekankan apa saja <em>major hazards</em> dan pengendaliannya yang harus disosialisasikan oleh <em>Permit Receiver</em> kepada seluruh pekerja terkait.
        </p>
    </div>

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100 text-center font-semibold">
            <tr>
                <th class="border px-2 py-1">Nama:</th>
                <th class="border px-2 py-1">Tanda tangan:</th>
                <th class="border px-2 py-1">Tanggal:</th>
                <th class="border px-2 py-1">Jam:</th>
            </tr>
        </thead>
        <tbody class="text-center text-xs">
            <tr>
                <td class="border px-2 py-2">
                    <input type="text" name="authorizer_name" class="border rounded px-2 py-1 w-full text-xs text-center" placeholder="Nama">
                </td>
                <td class="border px-2 py-2">
                    <button 
                        type="button" 
                        class="text-blue-600 underline text-xs"
                        @click="Alpine.store('signatureModal').openModal('Permit Authorizer')">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="authorizer_signature">
                </td>
                <td class="border px-2 py-2">
                    <input type="date" name="authorizer_date" class="border rounded px-2 py-1 w-full text-xs text-center">
                </td>
                <td class="border px-2 py-2">
                    <input type="time" name="authorizer_time" class="border rounded px-2 py-1 w-full text-xs text-center">
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
            Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah mensosialisasikan apa saja <em>major hazards</em> dan pengendaliannya dari pekerjaan ini kepada seluruh pekerja terkait.
        </p>
    </div>

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100 text-center font-semibold">
            <tr>
                <th class="border px-2 py-1">Nama:</th>
                <th class="border px-2 py-1">Tanda tangan:</th>
                <th class="border px-2 py-1">Tanggal:</th>
                <th class="border px-2 py-1">Jam:</th>
            </tr>
        </thead>
        <tbody class="text-center text-xs">
            <tr>
                <td class="border px-2 py-2">
                    <input type="text" name="receiver_name" class="border rounded px-2 py-1 w-full text-xs text-center" placeholder="Nama">
                </td>
                <td class="border px-2 py-2">
                    <button 
                        type="button" 
                        class="text-blue-600 underline text-xs"
                        @click="Alpine.store('signatureModal').openModal('Permit Receiver')">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="receiver_signature">
                </td>
                <td class="border px-2 py-2">
                    <input type="date" name="receiver_date" class="border rounded px-2 py-1 w-full text-xs text-center">
                </td>
                <td class="border px-2 py-2">
                    <input type="time" name="receiver_time" class="border rounded px-2 py-1 w-full text-xs text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- bagian 10 penutupan izin kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        10. Penutupan Izin Kerja
        <span class="text-xs font-normal italic float-right">(lingkari)</span>
    </h3>

    <!-- Checklist item lock & tag -->
    <table class="table-auto w-full text-sm border mt-2">
        <tbody>
            <tr>
                <td class="border px-2 py-1 italic w-1/4">Lock & Tag</td>
                <td class="border px-2 py-1">Semua <em>lock & tag</em> sudah dilepas</td>
                <td class="border px-2 py-1 text-center">Ya <input type="radio" name="lock_tag" value="ya"></td>
                <td class="border px-2 py-1 text-center">N/A <input type="radio" name="lock_tag" value="na"></td>
            </tr>
            <tr>
                <td class="border px-2 py-1 italic">Sampah & Peralatan Kerja</td>
                <td class="border px-2 py-1">Semua sampah sudah dibersihkan dan peralatan kerja sudah diamankan</td>
                <td class="border px-2 py-1 text-center">Ya <input type="radio" name="sampah_peralatan" value="ya"></td>
                <td class="border px-2 py-1 text-center">N/A <input type="radio" name="sampah_peralatan" value="na"></td>
            </tr>
            <tr>
                <td class="border px-2 py-1 italic">Machine Guarding</td>
                <td class="border px-2 py-1">Semua <em>machine guarding</em> sudah dipasang kembali</td>
                <td class="border px-2 py-1 text-center">Ya <input type="radio" name="machine_guarding" value="ya"></td>
                <td class="border px-2 py-1 text-center">N/A <input type="radio" name="machine_guarding" value="na"></td>
            </tr>
        </tbody>
    </table>

    <!-- Baris tanggal, jam, tanda tangan -->
    <table class="table-auto w-full text-sm border mt-4 text-center">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 w-1/4">Tanggal:</th>
                <th class="border px-2 py-1 w-1/4">Jam:</th>
                <th class="border px-2 py-1 w-1/4">Tanda Tangan<br><span class="italic">Permit Requestor</span></th>
                <th class="border px-2 py-1 w-1/4">Tanda Tangan<br><span class="italic">Permit Issuer</span></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-2">
                    <input type="date" name="penutupan_tanggal" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-2">
                    <input type="time" name="penutupan_jam" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-2">
                    <input type="text" name="requestor_name" class="input w-full text-xs text-center mb-1" placeholder="Nama">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Permit Requestor')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="requestor_signature">
                </td>
                <td class="border px-2 py-2">
                    <input type="text" name="issuer_name" class="input w-full text-xs text-center mb-1" placeholder="Nama">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Permit Issuer')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="issuer_signature">
                </td>
            </tr>
        </tbody>
    </table>
</div>
