<!-- bagian 1 izin kerja perancah -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h2 class="text-2xl font-bold text-center uppercase">IZIN KERJA PERANCAH</h2>
    <div class="text-center text-sm text-gray-600 mt-1">
        Izin kerja ini diberikan untuk semua pekerjaan pemasangan dan pembongkaran perancah. Pekerjaan tidak bisa dimulai hingga izin kerja diverifikasi oleh <em>Permit Verificator</em>, diterbitkan oleh <em>Permit Issuer</em>, disahkan oleh <em>Permit Authorizer</em> dan <em>major hazards & control</em> disosialisasikan oleh <em>Permit Receiver</em>.
    </div>

    <table class="table-auto w-full border text-sm mt-6">
        <thead class="bg-black text-white">
            <tr>
                <th colspan="2" class="border px-2 py-1 text-left font-bold">
                    1. Detail Pekerjaan Pemasangan/Pendirian Perancah
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-1 font-semibold w-1/2">Lokasi pekerjaan:</td>
                <td class="border px-2 py-1 font-semibold w-1/2">Tanggal:</td>
            </tr>
            <tr>
                <td class="border px-2 py-1">
                    <input type="text" name="lokasi_pekerjaan" class="input w-full text-sm">
                </td>
                <td class="border px-2 py-1">
                    <input type="date" name="tanggal_pekerjaan" class="input w-full text-sm">
                </td>
            </tr>
            <tr>
                <td class="border px-2 py-1 font-semibold" colspan="1">Uraian pekerjaan:</td>
                <td class="border px-2 py-1 font-semibold" colspan="1">Sketsa perancah yang akan dibangun:<br><span class="text-gray-400 text-xs italic">(Bisa lampiran terpisah)</span></td>
            </tr>
            <tr>
                <td class="border px-2 py-1">
                    <textarea name="uraian_pekerjaan" class="textarea w-full text-sm" rows="8"></textarea>
                </td>
                <td class="border px-2 py-1">
                    <textarea name="sketsa_perancah" class="textarea w-full text-sm" rows="8"></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="border px-2 py-1 font-semibold">
                    Peralatan/perlengkapan yang akan digunakan pada pekerjaan:
                </td>
            </tr>
            <tr>
                <td colspan="2" class="border px-2 py-1">
                    <textarea name="peralatan_digunakan" class="textarea w-full text-sm" rows="3"></textarea>
                </td>
            </tr>
            <tr>
                <td class="border px-2 py-1 font-semibold">Perkiraan jumlah pekerja yang akan terlibat dalam pekerjaan ini:</td>
                <td class="border px-2 py-1 font-semibold">Nomor gawat darurat yang harus dihubungi saat darurat:</td>
            </tr>
            <tr>
                <td class="border px-2 py-1">
                    <input type="number" name="jumlah_pekerja" class="input w-full text-sm">
                </td>
                <td class="border px-2 py-1">
                    <input type="text" name="nomor_darurat" class="input w-full text-sm">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- bagian 2 persyaratan kerja Aman -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        2. Persyaratan Kerja Aman untuk Pemasangan/Pendirian Perancah
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
                $persyaratanPerancah = [
                    'Area kerja sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi, area dipasang safety line/barikade.',
                    '<em>Job Safety Analysis/Safe Working Procedure</em> sudah tersedia untuk jenis perancah yang akan dipasang/didirikan.',
                    'Scaffolder memiliki sertifikasi sebagai Teknisi Perancah, memakai FBH double hook sesuai dan paham penggunaannya.',
                    'Scaffolder yang memasang perancah gantung dinyatakan fit untuk bekerja di ketinggian, perancah gantung memerlukan Izin Kerja Bekerja di Ketinggian.',
                    'Material perancah dan semua aksesorisnya dalam kondisi layak pakai.',
                    'Perancah kompleks (misalnya perancah di permukaan yang tingginya sampai puluhan lift) telah di review dan disetujui oleh <em>Civil Engineer</em> untuk dipasang/didirikan.'
                ];
            @endphp

            @foreach ($persyaratanPerancah as $index => $persyaratan)
                <tr>
                    <td class="border px-2 py-1">• {!! $persyaratan !!}</td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="persyaratan_perancah[{{ $index }}]" value="ya">
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="persyaratan_perancah[{{ $index }}]" value="na">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- bagian 3 permohonan izin kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        3. Permohonan Izin Kerja untuk Pemasangan/Pendirian Perancah
    </h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="text-sm italic font-semibold mb-2">Permit Requestor:</p>
        <p class="text-sm">
            Saya menyatakan bahwa semua persyaratan kerja aman untuk pemasangan/pendirian perancah yang telah ditentukan telah dipenuhi untuk dapat memulai memasang/mendirikan perancah ini.
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
                <td class="border text-center px-2 py-2">
                    <input type="text" name="permit_requestor_name" class="input w-full text-center">
                </td>
                <td class="border text-center px-2 py-2">
                    <button type="button"
                        @click="Alpine.store('signatureModal').openModal('Permit Requestor Perancah')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_permit_requestor_perancah">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="date" name="permit_requestor_date" class="input w-full text-center">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="time" name="permit_requestor_time" class="input w-full text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- bagian 4 verifikasi izin kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        4. Verifikasi Izin Kerja untuk Pemasangan/Pendirian Perancah
    </h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="text-sm italic font-semibold mb-2">Scaffolding Permit Verificator:</p>
        <p class="text-sm">
            Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman untuk pemasangan/pendirian perancah yang telah ditentukan telah dipenuhi untuk dapat memulai memasang/mendirikan perancah ini.
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
                <td class="border text-center px-2 py-2">
                    <input type="text" name="scaffolding_verificator_name" class="input w-full text-center">
                </td>
                <td class="border text-center px-2 py-2">
                    <button type="button"
                        @click="Alpine.store('signatureModal').openModal('Scaffolding Verificator')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_scaffolding_verificator">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="date" name="scaffolding_verificator_date" class="input w-full text-center">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="time" name="scaffolding_verificator_time" class="input w-full text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- bagian 5 penerbitan izin kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        5. Penerbitan Izin Kerja untuk Pemasangan/Pendirian Perancah
    </h3>

    <div class="border border-t-0 border-gray-300 p-3 text-sm">
        <p class="italic font-semibold mb-2">Permit Issuer:</p>
        <p>
            Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman 
            untuk pemasangan/pendirian perancah yang telah ditentukan telah dipenuhi untuk dapat memulai 
            memasang/mendirikan perancah ini.
        </p>
    </div>

    <!-- Tabel Nama dan Tanda Tangan Permit Issuer -->
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
                    <input type="text" name="permit_issuer_name" class="input w-full text-xs text-center">
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
                    <input type="date" name="permit_issuer_date" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_issuer_time" class="input w-full text-xs text-center">
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Baris Perancah Berlaku dari tanggal dan jam -->
    <table class="table-auto w-full text-sm border mt-4">
        <tbody>
            <tr>
                <td class="border text-right font-semibold px-2 py-1 whitespace-nowrap">
                    Izin kerja ini berlaku dari tanggal:
                </td>
                <td class="border px-2 py-1">
                    <input type="date" name="izin_berlaku_dari" class="input w-full text-xs">
                </td>
                <td class="border text-right font-semibold px-2 py-1 whitespace-nowrap">
                    jam:
                </td>
                <td class="border px-2 py-1">
                    <input type="time" name="izin_berlaku_jam_dari" class="input w-full text-xs">
                </td>
                <td class="border text-right font-semibold px-2 py-1 whitespace-nowrap">
                    sampai tanggal:
                </td>
                <td class="border px-2 py-1">
                    <input type="date" name="izin_berlaku_sampai" class="input w-full text-xs">
                </td>
                <td class="border text-right font-semibold px-2 py-1 whitespace-nowrap">
                    jam:
                </td>
                <td class="border px-2 py-1">
                    <input type="time" name="izin_berlaku_jam_sampai" class="input w-full text-xs">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- bagian 6 pengesehan izin kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        6. Pengesahan Izin Kerja untuk Pemasangan/Pendirian Perancah
    </h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="text-sm italic font-semibold mb-2">Permit Authorizer:</p>
        <p class="text-sm">
            Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman untuk pemasangan/pendirian perancah yang telah ditentukan telah dipenuhi untuk dapat memulai memasang/mendirikan perancah ini serta saya sudah menekankan apa saja <em>major hazards</em> dan pengendaliannya yang harus disosialisasikan oleh <em>Permit Receiver</em> kepada seluruh pekerja terkait.
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

<!-- bagian 7 pelaksanaan pekerjaan -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        7. Pelaksanaan Pekerjaan untuk Pemasangan/Pendirian Perancah
    </h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="text-sm italic font-semibold mb-2">Permit Receiver:</p>
        <p class="text-sm">
            Saya menyatakan bahwa semua persyaratan kerja aman untuk pemasangan/pendirian perancah yang telah ditentukan telah dipenuhi untuk dapat memulai memasang/mendirikan perancah ini serta saya sudah mensosialisasikan apa saja <em>major hazards</em> dan pengendaliannya dari pekerjaan ini kepada seluruh pekerja terkait.
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

<!-- bagian 8 persyaratan keselamatan -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        8. Persyaratan Keselamatan Perancah yang Dipasang/Didirikan
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
                $persyaratanKeselamatanPerancah = [
                    'Semua kaki-kaki perancah (<em>vertical standard</em>) dipasang tegak lurus, terpasang <em>base plate</em> di dasarnya dan menyentuh permukaan.',
                    'Semua ujung kaki-kaki perancah selain dipasang <em>base plate</em> juga dipasang <em>base pad</em> untuk meredam getaran/mencegah amblas.',
                    'Pemasangan <em>transom</em> dan <em>ledger</em> berada di dalam <em>vertical standard</em>, pemasangan <em>transom/putlog</em> di atas <em>ledger</em>.',
                    'Posisi baut <em>clamp</em> pengikat menghadap ke atas pada <em>ledger</em> yang diikatkan pada <em>vertical standard</em>.',
                    'Jarak antara <em>vertical standard</em> secara menyamping/memanjang (<em>bay</em>) dan jarak antar <em>ledger (lift)</em> sesuai dengan kelasnya <em>light duty/medium duty/heavy duty</em>.',
                    'Semua <em>bracing</em> yang diperlukan sudah terpasang.',
                    'Dipasang <em>outrigger</em> untuk menstabilkan perancah atau mengikatkannya dengan aman pada struktur yang kuat.',
                    '<em>Metal/wooden platform</em> yang dipasang kondisinya baik dan kuat menahan beban pekerja/material/peralatan, terikat kuat oleh kawat pengikat, dipasang sesuai tidak menimbulkan risiko tersandung, tidak ada celah terbuka yang bisa menimbulkan risiko terjatuh.',
                    'Pagar pengaman dan toe board terpasang pada setiap lantai perancah.',
                    'Tersedia tangga naik/turun dengan penempatan dan kemiringan yang aman, tangga melebihi 6 meter dipasang secara zig-zag, terikat kuat pada perancah.',
                    'Batas demarkasi/<em>Safety Line</em> telah dipasang di sekeliling bawah struktur perancah.',
                    'Kabel listrik dekat struktur perancah telah diamankan untuk mencegah risiko perancah teraliri aliran listrik.',
                    'Dipasang katrol yang aman untuk menaikkan/menurunkan barang.'
                ];
            @endphp

            @foreach ($persyaratanKeselamatanPerancah as $index => $persyaratan)
                <tr>
                    <td class="border px-2 py-1">• {!! $persyaratan !!}</td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="persyaratan_keselamatan_perancah[{{ $index }}]" value="ya">
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="persyaratan_keselamatan_perancah[{{ $index }}]" value="na">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- bagian 9 rekomendasi persyaratan -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        9. Rekomendasi Persyaratan Keselamatan Perancah Tambahan dari 
        <em>Permit Verificator/Permit Issuer</em> 
        <span class="text-xs font-normal">(jika ada)</span> 
        <span class="float-right italic">(lingkari)</span>
    </h3>

    <table class="table-auto w-full border text-sm mt-3">
        <tr>
            <td class="border px-2 py-2 align-top">
                <textarea name="rekomendasi_keselamatan_perancah" class="w-full h-32 border rounded p-2 resize-none" placeholder="Tulis rekomendasi jika ada..."></textarea>
            </td>
            <td class="border text-center align-top px-4 py-2 w-24">
                <label class="block mb-1 font-medium">Ya</label>
                <input type="radio" name="rekomendasi_status" value="ya">
            </td>
        </tr>
    </table>
</div>

<!-- bagian 10 persetujuan perancah -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        10. Persetujuan Perancah Layak Digunakan
    </h3>

    <div class="border border-t-0 border-gray-300 p-3 text-sm">
        <p><em>Scaffolding Permit Verificator, Permit Issuer & Permit Authorizer:</em></p>
        <p>
            Saya menyatakan bahwa saya telah memeriksa perancah dan semua persyaratan keselamatan perancah yang telah ditentukan 
            dan atau rekomendasi persyaratan keselamatan perancah tambahan dari <em>Permit Verificator/Permit Issuer</em> 
            telah dipenuhi untuk perancah ini dapat digunakan.
        </p>
    </div>

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center">Scaffolding Permit Verificator</th>
                <th class="border px-2 py-1 text-center">Permit Issuer</th>
                <th class="border px-2 py-1 text-center">Permit Authorizer</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-2 text-center">
                    <input type="text" name="scaffolding_verificator_name" class="input w-full text-center text-xs" placeholder="Nama">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="text" name="permit_issuer_name" class="input w-full text-center text-xs" placeholder="Nama">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="text" name="permit_authorizer_name" class="input w-full text-center text-xs" placeholder="Nama">
                </td>
            </tr>
        </tbody>
    </table>

    <table class="table-auto w-full text-sm border mt-2">
        <tbody>
            <tr>
                <td class="border px-2 py-1 text-left w-1/4 font-semibold">Perancah ini berlaku dari tanggal:</td>
                <td class="border px-2 py-1 text-center w-1/6">
                    <input type="date" name="perancah_start_date" class="input text-xs text-center">
                </td>
                <td class="border px-2 py-1 text-left w-1/12 font-semibold">jam:</td>
                <td class="border px-2 py-1 text-center w-1/6">
                    <input type="time" name="perancah_start_time" class="input text-xs text-center">
                </td>
                <td class="border px-2 py-1 text-left w-1/6 font-semibold">sampai tanggal:</td>
                <td class="border px-2 py-1 text-center w-1/6">
                    <input type="date" name="perancah_end_date" class="input text-xs text-center">
                </td>
                <td class="border px-2 py-1 text-left w-1/12 font-semibold">jam:</td>
                <td class="border px-2 py-1 text-center w-1/6">
                    <input type="time" name="perancah_end_time" class="input text-xs text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- bagian 11 penutupan izin kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        11. Penutupan Izin Kerja
        <span class="text-xs font-normal italic">(lingkari)</span>
    </h3>

    <!-- Checklist Lock & Tag, Sampah, Machine Guarding -->
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
                <td class="border px-2 py-1 font-bold italic">Lock & Tag</td>
                <td class="border px-2 py-1">Semua <em>lock & tag</em> sudah dilepas</td>
                <td class="border px-2 py-1 text-center">
                    <input type="radio" name="close_lock_tag" value="ya">
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="radio" name="close_lock_tag" value="na">
                </td>
            </tr>
            <tr>
                <td class="border px-2 py-1 font-bold italic">Sampah & Peralatan Kerja</td>
                <td class="border px-2 py-1">Semua sampah sudah dibersihkan dan peralatan kerja sudah diamankan</td>
                <td class="border px-2 py-1 text-center">
                    <input type="radio" name="close_sampah_peralatan" value="ya">
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="radio" name="close_sampah_peralatan" value="na">
                </td>
            </tr>
            <tr>
                <td class="border px-2 py-1 font-bold italic">Machine Guarding</td>
                <td class="border px-2 py-1">Semua <em>machine guarding</em> sudah dipasang kembali</td>
                <td class="border px-2 py-1 text-center">
                    <input type="radio" name="close_machine_guarding" value="ya">
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="radio" name="close_machine_guarding" value="na">
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel Tanggal, Jam, dan Tanda Tangan -->
    <table class="table-auto w-full text-sm border mt-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center w-1/4">Tanggal:</th>
                <th class="border px-2 py-1 text-center w-1/4">Jam:</th>
                <th class="border px-2 py-1 text-center" colspan="2">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="close_date" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="close_time" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <div class="font-bold italic">Permit Requestor</div>
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Penutup - Requestor')"
                        class="text-blue-600 underline text-xs mt-1">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_close_requestor">
                </td>
                <td class="border px-2 py-2 text-center">
                    <div class="font-bold italic">Permit Issuer</div>
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('Penutup - Issuer')"
                        class="text-blue-600 underline text-xs mt-1">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_close_issuer">
                </td>
            </tr>
        </tbody>
    </table>
</div>
