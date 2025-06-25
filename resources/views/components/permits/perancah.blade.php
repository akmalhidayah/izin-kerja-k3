<!-- Form Izin Kerja Perancah -->
<form method="POST" action="{{ route('working-permit.perancah.store') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="notification_id" value="{{ $notification->id ?? '' }}">

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
                <input type="text" name="lokasi_pekerjaan" class="input w-full text-sm"
                    value="{{ old('lokasi_pekerjaan', $detail?->location) }}">
            </td>
            <td class="border px-2 py-1">
                <input type="date" name="tanggal_pekerjaan" class="input w-full text-sm"
                    value="{{ old('tanggal_pekerjaan', $detail?->work_date) }}">
            </td>
        </tr>
        <tr>
            <td class="border px-2 py-1 font-semibold" colspan="1">Uraian pekerjaan:</td>
          <td class="border px-2 py-1 font-semibold" colspan="2">
    <label for="sketsa_perancah_file" class="block mb-1">
        Sketsa Perancah:
        <span class="text-gray-400 text-xs italic">(bisa berupa gambar/lampiran)</span>
    </label>

    <input 
        type="file" 
        name="sketsa_perancah_file" 
        id="sketsa_perancah_file" 
        accept="image/*" 
        class="text-sm block w-full border rounded px-2 py-1 focus:outline-none focus:ring"
    >

    @if (!empty($permit->sketsa_perancah) && file_exists(public_path($permit->sketsa_perancah)))
        <div class="mt-2">
            <span class="text-xs text-gray-500">Sketsa sebelumnya:</span><br>
            <img 
                src="{{ asset($permit->sketsa_perancah) }}" 
                alt="Sketsa Perancah" 
                class="max-w-xs border rounded mt-1"
            >
        </div>
    @endif
</td>


        </tr>
        <tr>
            <td class="border px-2 py-1">
                <textarea name="uraian_pekerjaan" class="textarea w-full text-sm" rows="8">{{ old('uraian_pekerjaan', $detail?->job_description) }}</textarea>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="border px-2 py-1 font-semibold">
                Peralatan/perlengkapan yang akan digunakan pada pekerjaan:
            </td>
        </tr>
        <tr>
            <td colspan="2" class="border px-2 py-1">
                <textarea name="peralatan_digunakan" class="textarea w-full text-sm" rows="3">{{ old('peralatan_digunakan', $detail?->equipment) }}</textarea>
            </td>
        </tr>
        <tr>
            <td class="border px-2 py-1 font-semibold">
                Perkiraan jumlah pekerja yang akan terlibat dalam pekerjaan ini:
            </td>
            <td class="border px-2 py-1 font-semibold">
                Nomor gawat darurat yang harus dihubungi saat darurat:
            </td>
        </tr>
        <tr>
            <td class="border px-2 py-1">
                <input type="number" name="jumlah_pekerja" class="input w-full text-sm"
                    value="{{ old('jumlah_pekerja', $detail?->worker_count) }}">
            </td>
            <td class="border px-2 py-1">
                <input type="text" name="nomor_darurat" class="input w-full text-sm"
                    value="{{ old('nomor_darurat', $detail?->emergency_contact) }}">
            </td>
        </tr>
    </tbody>
</table>

</div>

<!-- bagian 2 persyaratan kerja Aman -->
@php
    $persyaratanPerancahValues = old('persyaratan_perancah', json_decode($permit->persyaratan_perancah ?? '[]', true));
@endphp

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
                @php $selected = $persyaratanPerancahValues[$index] ?? null; @endphp
                <tr>
                    <td class="border px-2 py-1">• {!! $persyaratan !!}</td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="persyaratan_perancah[{{ $index }}]" value="ya" {{ $selected === 'ya' ? 'checked' : '' }}>
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="persyaratan_perancah[{{ $index }}]" value="na" {{ $selected === 'na' ? 'checked' : '' }}>
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
                    <input type="text" name="permit_requestor_name" class="input w-full text-center"
                        value="{{ old('permit_requestor_name', $permit->permit_requestor_name ?? '') }}">
                </td>
              <td class="border text-center px-2 py-2">
    <button type="button"
        onclick="openSignPad('signature_permit_requestor_perancah')"
        class="text-blue-600 underline text-xs">Tanda Tangan</button>
    <input type="hidden" name="signature_permit_requestor_perancah"
        id="signature_permit_requestor_perancah"
        value="{{ old('signature_permit_requestor_perancah', $permit->signature_permit_requestor_perancah ?? '') }}">
@if(old('signature_permit_requestor_perancah', $permit->signature_permit_requestor_perancah ?? null))
    <img src="{{ asset(old('signature_permit_requestor_perancah', $permit->signature_permit_requestor_perancah)) }}" alt="TTD" class="h-12 mx-auto mt-1">
@endif
</td>

                <td class="border text-center px-2 py-2">
                    <input type="date" name="permit_requestor_date" class="input w-full text-center"
                        value="{{ old('permit_requestor_date', $permit->permit_requestor_date ?? '') }}">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="time" name="permit_requestor_time" class="input w-full text-center"
                        value="{{ old('permit_requestor_time', $permit->permit_requestor_time ?? '') }}">
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
                    <input type="text" name="scaffolding_verificator_name" class="input w-full text-center"
                        value="{{ old('scaffolding_verificator_name', $permit->scaffolding_verificator_name ?? '') }}">
                </td>
               <td class="border text-center px-2 py-2">
    <button type="button"
        onclick="openSignPad('signature_scaffolding_verificator')"
        class="text-blue-600 underline text-xs">Tanda Tangan</button>
    <input type="hidden" name="signature_scaffolding_verificator"
        id="signature_scaffolding_verificator"
        value="{{ old('signature_scaffolding_verificator', $permit->signature_scaffolding_verificator ?? '') }}">
@if(old('signature_scaffolding_verificator', $permit->signature_scaffolding_verificator ?? null))
    <img src="{{ asset(old('signature_scaffolding_verificator', $permit->signature_scaffolding_verificator)) }}" alt="TTD" class="h-12 mx-auto mt-1">
@endif
</td>

                <td class="border text-center px-2 py-2">
                    <input type="date" name="scaffolding_verificator_date" class="input w-full text-center"
                        value="{{ old('scaffolding_verificator_date', $permit->scaffolding_verificator_date ?? '') }}">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="time" name="scaffolding_verificator_time" class="input w-full text-center"
                        value="{{ old('scaffolding_verificator_time', $permit->scaffolding_verificator_time ?? '') }}">
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
                    <input type="text" name="permit_issuer_name" class="input w-full text-xs text-center"
                        value="{{ old('permit_issuer_name', $permit->permit_issuer_name ?? '') }}">
                </td>
           <td class="border px-2 py-2 text-center">
    <button type="button"
        onclick="openSignPad('perancah_signature_permit_issuer')"
        class="text-blue-600 underline text-xs">Tanda Tangan</button>
    <input type="hidden" name="signature_permit_issuer"
        id="perancah_signature_permit_issuer"
        value="{{ old('signature_permit_issuer', $permit->signature_permit_issuer ?? '') }}">
@if(old('signature_permit_issuer', $permit->signature_permit_issuer ?? null))
    <img src="{{ asset(old('signature_permit_issuer', $permit->signature_permit_issuer)) }}" alt="TTD" class="h-12 mx-auto mt-1">
@endif
</td>

                <td class="border px-2 py-2 text-center">
                    <input type="date" name="permit_issuer_date" class="input w-full text-xs text-center"
                        value="{{ old('permit_issuer_date', $permit->permit_issuer_date ?? '') }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_issuer_time" class="input w-full text-xs text-center"
                        value="{{ old('permit_issuer_time', $permit->permit_issuer_time ?? '') }}">
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Baris Perancah Berlaku dari tanggal dan jam -->
    <table class="table-auto w-full text-sm border mt-4">
        <tbody>
            <tr>
                <td class="border text-right font-semibold px-2 py-1 whitespace-nowrap">Izin kerja ini berlaku dari tanggal:</td>
                <td class="border px-2 py-1">
                    <input type="date" name="izin_berlaku_dari" class="input w-full text-xs"
                        value="{{ old('izin_berlaku_dari', $permit->izin_berlaku_dari ?? '') }}">
                </td>
                <td class="border text-right font-semibold px-2 py-1 whitespace-nowrap">jam:</td>
                <td class="border px-2 py-1">
                    <input type="time" name="izin_berlaku_jam_dari" class="input w-full text-xs"
                        value="{{ old('izin_berlaku_jam_dari', $permit->izin_berlaku_jam_dari ?? '') }}">
                </td>
                <td class="border text-right font-semibold px-2 py-1 whitespace-nowrap">sampai tanggal:</td>
                <td class="border px-2 py-1">
                    <input type="date" name="izin_berlaku_sampai" class="input w-full text-xs"
                        value="{{ old('izin_berlaku_sampai', $permit->izin_berlaku_sampai ?? '') }}">
                </td>
                <td class="border text-right font-semibold px-2 py-1 whitespace-nowrap">jam:</td>
                <td class="border px-2 py-1">
                    <input type="time" name="izin_berlaku_jam_sampai" class="input w-full text-xs"
                        value="{{ old('izin_berlaku_jam_sampai', $permit->izin_berlaku_jam_sampai ?? '') }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- bagian 6 pengesahan izin kerja -->
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
                    <input type="text" name="permit_authorizer_name" class="input w-full text-xs text-center"
                        value="{{ old('permit_authorizer_name', $permit->permit_authorizer_name ?? '') }}">
               <td class="border text-center px-2 py-2">
    <button type="button"
        onclick="openSignPad('signature_permit_authorizer_perancah')"
        class="text-blue-600 underline text-xs">
        Tanda Tangan
    </button>
    <input type="hidden" name="signature_permit_authorizer"
        id="signature_permit_authorizer_perancah"
        value="{{ old('signature_permit_authorizer', $permit->signature_permit_authorizer ?? '') }}">
    @if(old('signature_permit_authorizer', $permit->signature_permit_authorizer ?? null))
        <img src="{{ asset(old('signature_permit_authorizer', $permit->signature_permit_authorizer)) }}" alt="TTD" class="h-12 mx-auto mt-1">
    @endif
</td>

                <td class="border text-center px-2 py-2">
                    <input type="date" name="permit_authorizer_date" class="input w-full text-xs text-center"
                        value="{{ old('permit_authorizer_date', $permit->permit_authorizer_date ?? '') }}">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="time" name="permit_authorizer_time" class="input w-full text-xs text-center"
                        value="{{ old('permit_authorizer_time', $permit->permit_authorizer_time ?? '') }}">
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
                    <input type="text" name="permit_receiver_name" class="input w-full text-xs text-center"
                        value="{{ old('permit_receiver_name', $permit->permit_receiver_name ?? '') }}">
                </td>
               <td class="border text-center px-2 py-2">
    <button type="button"
        onclick="openSignPad('signature_permit_receiver_perancah')"
        class="text-blue-600 underline text-xs">
        Tanda Tangan
    </button>
    <input type="hidden" name="signature_permit_receiver"
        id="signature_permit_receiver_perancah"
        value="{{ old('signature_permit_receiver', $permit->signature_permit_receiver ?? '') }}">
    @if(old('signature_permit_receiver', $permit->signature_permit_receiver ?? null))
        <img src="{{ asset(old('signature_permit_receiver', $permit->signature_permit_receiver)) }}" alt="TTD" class="h-12 mx-auto mt-1">
    @endif
</td>

                <td class="border text-center px-2 py-2">
                    <input type="date" name="permit_receiver_date" class="input w-full text-xs text-center"
                        value="{{ old('permit_receiver_date', $permit->permit_receiver_date ?? '') }}">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="time" name="permit_receiver_time" class="input w-full text-xs text-center"
                        value="{{ old('permit_receiver_time', $permit->permit_receiver_time ?? '') }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- bagian 8 persyaratan keselamatan perancah -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        8. Persyaratan Keselamatan Perancah yang Dipasang/Didirikan
        <span class="text-xs font-normal italic">(lingkari)</span>
    </h3>

    @php
        $persyaratanKeselamatanValues = old('persyaratan_keselamatan_perancah', json_decode($permit->persyaratan_keselamatan_perancah ?? '[]', true));
    @endphp
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
                    'Jarak antar <em>vertical standard</em> dan jarak antar <em>ledger (lift)</em> sesuai dengan kelasnya <em>light/medium/heavy duty</em>.',
                    'Semua <em>bracing</em> yang diperlukan sudah terpasang.',
                    'Dipasang <em>outrigger</em> untuk menstabilkan perancah atau mengikatkannya dengan aman.',
                    '<em>Metal/wooden platform</em> yang dipasang dalam kondisi baik dan kuat menahan pekerja/material, terikat kawat pengikat, terpasang sesuai agar tidak menimbulkan risiko tersandung, tidak ada celah terbuka.',
                    'Pagar pengaman dan toe board terpasang pada setiap lantai perancah.',
                    'Tersedia tangga naik/turun dengan penempatan dan kemiringan yang aman, tangga di atas 6 meter dipasang zig-zag dan terikat kuat pada perancah.',
                    'Batas demarkasi/<em>Safety Line</em> telah dipasang di sekeliling bawah perancah.',
                    'Kabel listrik dekat perancah telah diamankan untuk mencegah perancah teraliri listrik.',
                    'Dipasang katrol yang aman untuk menaikkan/menurunkan barang.'
                ];
            @endphp

            @foreach ($persyaratanKeselamatanPerancah as $index => $persyaratan)
                @php
                    $selectedKeselamatan = $persyaratanKeselamatanValues[$index] ?? null;
                @endphp
                <tr>
                    <td class="border px-2 py-1">• {!! $persyaratan !!}</td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="persyaratan_keselamatan_perancah[{{ $index }}]" value="ya"
                            {{ $selectedKeselamatan === 'ya' ? 'checked' : '' }}>
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="persyaratan_keselamatan_perancah[{{ $index }}]" value="na"
                            {{ $selectedKeselamatan === 'na' ? 'checked' : '' }}>
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
                <textarea name="rekomendasi_keselamatan_perancah"
                    class="w-full h-32 border rounded p-2 resize-none"
                    placeholder="Tulis rekomendasi jika ada...">{{ old('rekomendasi_keselamatan_perancah', $permit->rekomendasi_keselamatan_perancah ?? '') }}</textarea>
            </td>
            <td class="border text-center align-top px-4 py-2 w-24">
                <label class="block mb-1 font-medium">Ya</label>
                <input type="radio" name="rekomendasi_status" value="ya"
                    {{ old('rekomendasi_status', $permit->rekomendasi_status ?? '') === 'ya' ? 'checked' : '' }}>
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
            <!-- Scaffolding Verificator -->
                <td class="border px-2 py-2 text-center">
                    <input type="text" name="scaffolding_verificator_approval" class="input w-full text-xs text-center"
                        placeholder="Nama"
                        value="{{ old('scaffolding_verificator_approval', $permit->scaffolding_verificator_approval ?? '') }}">

                    <button type="button"
                        onclick="openSignPad('perancah_signature_verificator_approval')"
                        class="text-blue-600 underline text-xs mt-1">Tanda Tangan</button>

                    <input type="hidden" name="signature_verificator_approval"
                        id="perancah_signature_verificator_approval"
                        value="{{ old('signature_verificator_approval', $permit->signature_verificator_approval ?? '') }}">

                    @if(old('signature_verificator_approval', $permit->signature_verificator_approval ?? null))
                        <img src="{{ asset(old('signature_verificator_approval', $permit->signature_verificator_approval)) }}" class="h-12 mx-auto mt-1">
                    @endif
                </td>

                <!-- Permit Issuer -->
                <td class="border px-2 py-2 text-center">
                    <input type="text" name="permit_issuer_approval" class="input w-full text-xs text-center"
                        placeholder="Nama"
                        value="{{ old('permit_issuer_approval', $permit->permit_issuer_approval ?? '') }}">

                    <button type="button"
                        onclick="openSignPad('perancah_signature_issuer_approval')"
                        class="text-blue-600 underline text-xs mt-1">Tanda Tangan</button>

                    <input type="hidden" name="signature_issuer_approval"
                        id="perancah_signature_issuer_approval"
                        value="{{ old('signature_issuer_approval', $permit->signature_issuer_approval ?? '') }}">

                    @if(old('signature_issuer_approval', $permit->signature_issuer_approval ?? null))
                        <img src="{{ asset(old('signature_issuer_approval', $permit->signature_issuer_approval)) }}" class="h-12 mx-auto mt-1">
                    @endif
                </td>

                <!-- Permit Authorizer -->
                <td class="border px-2 py-2 text-center">
                    <input type="text" name="permit_authorizer_approval" class="input w-full text-xs text-center"
                        placeholder="Nama"
                        value="{{ old('permit_authorizer_approval', $permit->permit_authorizer_approval ?? '') }}">

                    <button type="button"
                        onclick="openSignPad('perancah_signature_authorizer_approval')"
                        class="text-blue-600 underline text-xs mt-1">Tanda Tangan</button>

                    <input type="hidden" name="signature_authorizer_approval"
                        id="perancah_signature_authorizer_approval"
                        value="{{ old('signature_authorizer_approval', $permit->signature_authorizer_approval ?? '') }}">

                    @if(old('signature_authorizer_approval', $permit->signature_authorizer_approval ?? null))
                        <img src="{{ asset(old('signature_authorizer_approval', $permit->signature_authorizer_approval)) }}" class="h-12 mx-auto mt-1">
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <table class="table-auto w-full text-sm border mt-2">
        <tbody>
            <tr>
                <td class="border px-2 py-1 text-left w-1/4 font-semibold">Perancah ini berlaku dari tanggal:</td>
                <td class="border px-2 py-1 text-center w-1/6">
                    <input type="date" name="perancah_start_date" class="input text-xs text-center"
                        value="{{ old('perancah_start_date', $permit->perancah_start_date ?? '') }}">
                </td>
                <td class="border px-2 py-1 text-left w-1/12 font-semibold">jam:</td>
                <td class="border px-2 py-1 text-center w-1/6">
                    <input type="time" name="perancah_start_time" class="input text-xs text-center"
                        value="{{ old('perancah_start_time', $permit->perancah_start_time ?? '') }}">
                </td>
                <td class="border px-2 py-1 text-left w-1/6 font-semibold">sampai tanggal:</td>
                <td class="border px-2 py-1 text-center w-1/6">
                    <input type="date" name="perancah_end_date" class="input text-xs text-center"
                        value="{{ old('perancah_end_date', $permit->perancah_end_date ?? '') }}">
                </td>
                <td class="border px-2 py-1 text-left w-1/12 font-semibold">jam:</td>
                <td class="border px-2 py-1 text-center w-1/6">
                    <input type="time" name="perancah_end_time" class="input text-xs text-center"
                        value="{{ old('perancah_end_time', $permit->perancah_end_time ?? '') }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>


@php
    $closeDate = old('close_date') ?? ($closure?->closed_date ? \Carbon\Carbon::parse($closure->closed_date)->format('Y-m-d') : '');
    $closeTime = old('close_time') ?? $closure?->closed_time;
    $closeRequestorName = old('close_requestor_name') ?? $closure?->requestor_name;
    $closeIssuerName = old('close_issuer_name') ?? $closure?->issuer_name;
    $closeRequestorSign = old('signature_close_requestor') ?? ($closure?->requestor_sign ?? '');
    $closeIssuerSign = old('signature_close_issuer') ?? ($closure?->issuer_sign ?? '');
    $jumlahRfid = old('jumlah_rfid') ?? $closure?->jumlah_rfid;

    $closeLock = old('close_lock_tag') ?? ($closure?->lock_tag_removed ? 'ya' : 'na');
    $closeTools = old('close_tools') ?? ($closure?->equipment_cleaned ? 'ya' : 'na');
    $closeGuarding = old('close_guarding') ?? ($closure?->guarding_restored ? 'ya' : 'na');
@endphp

<!-- Bagian 11: Penutupan Izin Kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">11. Penutupan Izin Kerja</h3>

    <!-- Checklist -->
    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-left">Item</th>
                <th class="border px-2 py-1 text-left">Keterangan</th>
                <th class="border px-2 py-1 text-center w-20">(O)</th>
            </tr>
        </thead>
        <tbody>
            @foreach([
                'close_lock_tag' => ['label' => 'Lock & Tag', 'desc' => 'Semua <em>lock & tag</em> sudah dilepas', 'val' => $closeLock],
                'close_tools' => ['label' => 'Sampah & Peralatan Kerja', 'desc' => 'Semua sampah sudah dibersihkan dan peralatan kerja sudah diamankan', 'val' => $closeTools],
                'close_guarding' => ['label' => 'Machine Guarding', 'desc' => 'Semua <em>machine guarding</em> sudah dipasang kembali', 'val' => $closeGuarding],
            ] as $name => $item)
            <tr>
                <td class="border px-2 py-1 font-semibold">{{ $item['label'] }}</td>
                <td class="border px-2 py-1">{!! $item['desc'] !!}</td>
                <td class="border px-2 py-1 text-center">
                    <label><input type="radio" name="{{ $name }}" value="ya" {{ $item['val'] === 'ya' ? 'checked' : '' }}> Ya</label>
                    <label class="ml-2"><input type="radio" name="{{ $name }}" value="na" {{ $item['val'] === 'na' ? 'checked' : '' }}> N/A</label>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tanggal, Jam, TTD -->
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
                    <input type="date" name="close_date" class="input w-full text-center" value="{{ $closeDate }}">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="time" name="close_time" class="input w-full text-center" value="{{ $closeTime }}">
                </td>

                <!-- Requestor -->
                <td class="border text-center px-2 py-2">
                    <input type="text" name="close_requestor_name" class="input w-full text-xs mb-1" placeholder="Nama" value="{{ $closeRequestorName }}">
                    @if ($closeRequestorSign && file_exists(public_path($closeRequestorSign)))
                        <img src="{{ asset($closeRequestorSign) }}" alt="Tanda Tangan" class="h-20 mx-auto">
                    @else
                        <button type="button" onclick="openSignPad('signature_close_requestor')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    @endif
                    <input type="hidden" name="signature_close_requestor" id="signature_close_requestor" value="{{ $closeRequestorSign }}">
                </td>

                <!-- Issuer -->
                <td class="border text-center px-2 py-2">
                    <input type="text" name="close_issuer_name" class="input w-full text-xs mb-1" placeholder="Nama" value="{{ $closeIssuerName }}">
                    @if ($closeIssuerSign && file_exists(public_path($closeIssuerSign)))
                        <img src="{{ asset($closeIssuerSign) }}" alt="Tanda Tangan" class="h-20 mx-auto">
                    @else
                        <button type="button" onclick="openSignPad('signature_close_issuer')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    @endif
                    <input type="hidden" name="signature_close_issuer" id="signature_close_issuer" value="{{ $closeIssuerSign }}">
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Jumlah RFID -->
    <table class="table-auto w-full text-sm border mt-4">
        <tr>
            <td class="border px-2 py-1 font-semibold w-64">Jumlah RFID yang diberikan ke kontraktor</td>
            <td class="border px-2 py-1 text-left" colspan="3">
                <div class="flex items-center gap-2">
                    <input type="number" name="jumlah_rfid" min="0" class="input w-28 text-center"
                        value="{{ $jumlahRfid }}">
                    <span class="text-sm">buah</span>
                </div>
            </td>
        </tr>
    </table>
</div>

<!-- Tombol Simpan -->
<div class="flex justify-center mt-8">
    <button type="submit" name="action" value="save"
        class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-6 rounded shadow-md transition duration-200">
        💾 Simpan
    </button>
</div>
</form>

