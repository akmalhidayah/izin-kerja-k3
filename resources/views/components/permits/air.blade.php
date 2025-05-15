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

<!-- 
bagian 3 persyaratan kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        3. Persyaratan Kerja Aman <span class="text-xs italic font-normal">(lingkari)</span>
    </h3>

    <table class="table-auto w-full border text-sm mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-left">Persyaratan</th>
                <th class="border px-2 py-1 text-center w-12">Ya</th>
                <th class="border px-2 py-1 text-center w-12">N/A</th>
            </tr>
        </thead>
        <tbody>
            @php
                $persyaratanPerairan = [
                    'Area kerja sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
                    'Area tepi lantai kerja terbuka yang bisa sebabkan pekerja tenggelam di air sudah diamankan memasang pagar sementara/barikade yang mampu menahan beban pekerja, jika tidak memungkinkan minimum dipasang safety line/marka dengan rambu peringatan yang mengingatkan akan risiko tenggelam.',
                    'Area kerja sudah diamankan dari potensi pekerja terpeleset/tersandung.',
                    'Job Safety Analysis/Safe Working Procedure sudah tersedia dan semua pengendalian bahayanya sudah dilakukan.',
                    'Bukaan di lantai sudah dipasang pagar pengaman sementara/ditutup yang mencegah pekerja terjatuh ke air.',
                    'Pekerja menggunakan full body harness yang difungsikan sebagai fall restraint system sehingga membatasi pekerja untuk mendekati area yang bisa menyebabkan tenggelam.',
                    'Life jacket sudah diperiksa dan dinyatakan aman untuk dipakai.',
                    'Semua pekerja yang berpotensi tenggelam bisa berenang dan sudah menggunakan life jacket yang sesuai.',
                    'Semua pekerja yang terlibat terlatih, memahami bahaya, risiko dan pengendaliannya untuk bekerja di air.',
                    'Semua pekerja penyelaman di air kompeten dan sudah dilatih untuk pekerjaan tersebut.',
                    'Semua pekerja terlibat memahami teknik three point contact saat naik/turun tangga (contohnya tangga dermaga).',
                    'Tim Rescue tersedia dan bisa segera datang melakukan pertolongan jika terjadi kondisi korban tenggelam.',
                    'Peralatan/perlengkapan kerja yang akan digunakan sudah diperiksa dan dipastikan layak dan aman untuk digunakan.',
                    'Peralatan pertolongan untuk menolong korban tenggelam tersedia seperti, ringbuoy/lifebuoy, throwable device, basket stretcher dll.',
                    'SCBA yang akan digunakan sudah diperiksa, dan layak untuk dipakai dan digunakan oleh penyelam.',
                    'Boat/rubber boat beserta seluruh kelengkapannya yang akan digunakan sudah diperiksa dan dinyatakan layak untuk digunakan.'
                ];
            @endphp

            @foreach ($persyaratanPerairan as $i => $item)
                <tr>
                    <td class="border px-2 py-1">• {{ $item }}</td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="perairan[{{ $i }}]" value="ya">
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="perairan[{{ $i }}]" value="na">
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
        <span class="float-right">(lingkari)</span>
    </h3>

    <table class="table-auto w-full border text-sm mt-2">
        <tr>
            <td class="border p-2" colspan="2">
                <textarea name="rekomendasi_kerja_aman" rows="6" class="w-full border-none text-sm focus:ring-0 focus:outline-none" placeholder="Tulis rekomendasi tambahan jika ada..."></textarea>
            </td>
            <td class="border text-center align-top" style="width: 60px;">
                <label class="inline-flex items-center">
                    <input type="radio" name="rekomendasi_kerja_aman_check" value="Ya" class="mr-1"> Ya
                </label>
            </td>
        </tr>
    </table>
</div>

<!-- bagian 5 permohonan izin kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">5. Permohonan Izin Kerja</h3>

    <div class="bg-gray-100 border border-t-0 border-gray-300 p-3">
        <p class="italic font-semibold mb-1">Permit Requestor:</p>
        <p class="text-sm">
            Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk dapat melakukan pekerjaan ini.
        </p>
    </div>

    <table class="table-auto w-full border text-sm mt-3">
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
                    <input type="text" name="permit_requestor_name" class="input w-full text-xs text-center" placeholder="Nama">
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

<!-- bagian 6 verifikasi izin kerja -->
<div x-data="{ workers: [''] }" class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-xs">
    <h3 class="font-bold bg-black text-white px-2 py-1">6. Verifikasi Izin Kerja</h3>

    <div class="bg-gray-100 border border-t-0 border-gray-300 p-3">
        <p class="italic font-semibold mb-1">Working at Water Permit Verificator:</p>
        <p class="text-sm">
            Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
            <strong> Berikut nama-nama pekerja yang diizinkan untuk bekerja di air:</strong>
        </p>
    </div>

    <!-- Tabel Nama Pekerja Dinamis (1 kolom) -->
    <table class="table-auto w-full border text-xs mt-2">
        <tbody>
            <template x-for="(worker, index) in workers" :key="index">
                <tr>
                    <td class="border px-2 py-1 w-full">
                        <input type="text" :name="`pekerja_air_${index}`" x-model="workers[index]" class="input w-full text-xs" placeholder="Nama Pekerja">
                    </td>
                    <td class="border px-2 py-1">
                        <button type="button" @click="workers.splice(index, 1)" class="text-red-600 text-xs">Hapus</button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
    <div class="mt-2">
        <button type="button" @click="workers.push('')"
            class="bg-blue-500 text-white px-2 py-1 rounded text-xs">
            + Tambah Pekerja
        </button>
    </div>

    <!-- Verifikator -->
    <table class="table-auto w-full border text-xs mt-4">
        <thead class="bg-gray-100 text-center">
            <tr>
                <th class="border px-2 py-1">Nama:</th>
                <th class="border px-2 py-1">Tanda tangan:</th>
                <th class="border px-2 py-1">Tanggal:</th>
                <th class="border px-2 py-1">Jam:</th>
            </tr>
        </thead>
        <tbody class="text-center">
            <tr>
                <td class="border px-2 py-2">
                    <input type="text" name="verifikator_air_nama" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-2">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Working at Water Permit Verificator')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_verifikator_air">
                </td>
                <td class="border px-2 py-2">
                    <input type="date" name="verifikator_air_tanggal" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-2">
                    <input type="time" name="verifikator_air_jam" class="input w-full text-xs text-center">
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
<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        8. Pengesahan Izin Kerja
    </h3>

    <div class="bg-gray-100 border border-t-0 border-gray-300 p-3">
        <p class="italic font-semibold mb-1">Permit Authorizer:</p>
        <p class="text-sm">
            Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah menekankan apa saja 
            <em>major hazards</em> dan pengendaliannya yang harus disosialisasikan oleh 
            <em>Permit Receiver</em> kepada seluruh pekerja terkait.
        </p>
    </div>

    <!-- Tabel Input Data Authorizer -->
    <table class="table-auto w-full border text-sm mt-3">
        <thead class="bg-gray-100 text-center">
            <tr>
                <th class="border px-2 py-1">Nama:</th>
                <th class="border px-2 py-1">Tanda tangan:</th>
                <th class="border px-2 py-1">Tanggal:</th>
                <th class="border px-2 py-1">Jam:</th>
            </tr>
        </thead>
        <tbody class="text-center">
            <tr>
                <td class="border px-2 py-2">
                    <input type="text" name="permit_authorizer_name" class="input w-full text-xs text-center" placeholder="Nama">
                </td>
                <td class="border px-2 py-2">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Permit Authorizer')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_permit_authorizer">
                </td>
                <td class="border px-2 py-2">
                    <input type="date" name="permit_authorizer_date" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-2">
                    <input type="time" name="permit_authorizer_time" class="input w-full text-xs text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- bagian 9 pelaksanaan pekerjaan -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        9. Pelaksanaan Pekerjaan
    </h3>

    <div class="bg-gray-100 border border-t-0 border-gray-300 p-3">
        <p class="italic font-semibold mb-1">Permit Receiver:</p>
        <p class="text-sm">
            Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah mensosialisasikan apa saja 
            <em>major hazards</em> dan pengendaliannya dari pekerjaan ini kepada seluruh pekerja terkait.
        </p>
    </div>

    <!-- Tabel Input Data Permit Receiver -->
    <table class="table-auto w-full border text-sm mt-3">
        <thead class="bg-gray-100 text-center">
            <tr>
                <th class="border px-2 py-1">Nama:</th>
                <th class="border px-2 py-1">Tanda tangan:</th>
                <th class="border px-2 py-1">Tanggal:</th>
                <th class="border px-2 py-1">Jam:</th>
            </tr>
        </thead>
        <tbody class="text-center">
            <tr>
                <td class="border px-2 py-2">
                    <input type="text" name="permit_receiver_name" class="input w-full text-xs text-center" placeholder="Nama">
                </td>
                <td class="border px-2 py-2">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Permit Receiver')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_permit_receiver">
                </td>
                <td class="border px-2 py-2">
                    <input type="date" name="permit_receiver_date" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-2">
                    <input type="time" name="permit_receiver_time" class="input w-full text-xs text-center">
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
