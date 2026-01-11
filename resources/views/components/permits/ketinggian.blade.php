@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded relative mb-4 text-sm">
        {{ session('success') }}
    </div>
@endif

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-sm text-gray-800 dark:text-gray-200 leading-tight">
            WORKING PERMIT KETINGGIAN
        </h2>
    </x-slot>

    <section class="bg-cover bg-center bg-no-repeat py-10 px-4" style="background-image: url('/images/bg-login.jpg');">
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-6">
<form method="POST" action="{{ route('working-permit.ruangtertutup.store') }}" enctype="multipart/form-data">
 @csrf

<input type="hidden" name="notification_id" value="{{ $notification->id ?? '' }}">
<input type="hidden" name="clear_all_signatures" id="clear_all_signatures" value="0">
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
{{-- BAGIAN 1 --}}
<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 space-y-4">
    <h3 class="bg-black text-white px-2 py-1 font-bold">1. Detail Pekerjaan</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
            <label class="font-semibold">Lokasi pekerjaan:</label>
            <input type="text" name="lokasi_pekerjaan" class="input w-full text-sm"
                value="{{ old('lokasi_pekerjaan', $detail?->location) }}">
        </div>
        <div>
            <label class="font-semibold">Tanggal:</label>
            <input type="date" name="tanggal_pekerjaan" class="input w-full text-sm"
                value="{{ old('tanggal_pekerjaan', $detail?->work_date ? \Carbon\Carbon::parse($detail->work_date)->format('Y-m-d') : '') }}">
        </div>
    </div>

    <div>
        <label class="font-semibold">Uraian pekerjaan:</label>
        <textarea name="uraian_pekerjaan" class="textarea w-full text-sm" rows="3">{{ old('uraian_pekerjaan', $detail?->job_description) }}</textarea>
    </div>

    <div>
        <label class="font-semibold">Peralatan/perlengkapan yang akan digunakan pada pekerjaan:</label>
        <textarea name="peralatan_digunakan" class="textarea w-full text-sm" rows="2">{{ old('peralatan_digunakan', $detail?->equipment) }}</textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
            <label class="font-semibold">Perkiraan jumlah pekerja yang akan terlibat:</label>
            <input type="number" name="jumlah_pekerja" class="input w-full text-sm"
                value="{{ old('jumlah_pekerja', $detail?->worker_count) }}">
        </div>
        <div>
            <label class="font-semibold">Nomor gawat darurat yang dapat dihubungi:</label>
            <input type="text" name="nomor_darurat" class="input w-full text-sm"
                value="{{ old('nomor_darurat', $detail?->emergency_contact) }}">
        </div>
    </div>
</div>
@php
    $rawListrik = old('isolasi_listrik') ?? $permit->isolasi_listrik ?? [];
    $listrik = is_string($rawListrik) ? json_decode($rawListrik, true) : $rawListrik;

    if (!is_array($listrik) || empty($listrik)) {
        $listrik = [['peralatan' => '', 'nomor' => '', 'tempat' => '', 'locked' => '', 'tested' => '', 'signature' => '']];
    }

    $rawNonListrik = old('isolasi_non_listrik') ?? $permit->isolasi_non_listrik ?? [];
    $nonListrik = is_string($rawNonListrik) ? json_decode($rawNonListrik, true) : $rawNonListrik;

    if (!is_array($nonListrik) || empty($nonListrik)) {
        $nonListrik = [['peralatan' => '', 'jenis' => '', 'tempat' => '', 'locked' => '', 'tested' => '', 'signature' => '']];
    }

    $jsonListrik = json_encode($listrik);
    $jsonNonListrik = json_encode($nonListrik);
@endphp

<div
    x-data="{
        listrik: JSON.parse(@js($jsonListrik)),
        nonListrik: JSON.parse(@js($jsonNonListrik)),

        addListrik() {
            this.listrik.push({ peralatan: '', nomor: '', tempat: '', locked: '', tested: '', signature: '' });
        },
        addNonListrik() {
            this.nonListrik.push({ peralatan: '', jenis: '', tempat: '', locked: '', tested: '', signature: '' });
        }
    }"
    class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6"
>
    <h3 class="font-bold bg-black text-white px-2 py-1">
        2. Titik Isolasi dan Penguncian jika Diperlukan 
        <span class="text-xs font-normal italic">(diisi oleh <strong>Isolation Officer</strong>)</span>
    </h3>

    <!-- ISOLASI LISTRIK -->
    <h4 class="font-semibold mt-2">Isolasi Energi Listrik</h4>
    <table class="table-auto w-full text-sm border mt-2">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-2">Peralatan / Mesin</th>
                <th class="border p-2">Nomor</th>
                <th class="border p-2">Tempat</th>
                <th class="border p-2">Locked, Tagged</th>
                <th class="border p-2">Tested</th>
                <th class="border p-2">TTD</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(row, i) in listrik" :key="i">
                <tr>
                    <td class="border p-1"><input type="text" :name="'isolasi_listrik['+i+'][peralatan]'" x-model="row.peralatan" class="input w-full"></td>
                    <td class="border p-1"><input type="text" :name="'isolasi_listrik['+i+'][nomor]'" x-model="row.nomor" class="input w-full"></td>
                    <td class="border p-1"><input type="text" :name="'isolasi_listrik['+i+'][tempat]'" x-model="row.tempat" class="input w-full"></td>
                    <td class="border p-1"><input type="text" :name="'isolasi_listrik['+i+'][locked]'" x-model="row.locked" class="input w-full"></td>
                    <td class="border p-1"><input type="text" :name="'isolasi_listrik['+i+'][tested]'" x-model="row.tested" class="input w-full"></td>
                    <td class="border p-1 text-center">
                        <template x-if="row.signature">
        <img :src="row.signature" class="h-10 mx-auto mb-1">
    </template>
                        <button type="button" @click="openSignPad('isolasi_listrik_signature_' + i)" class="text-blue-600 underline text-xs">TTD</button>
                        <input type="hidden" :id="'isolasi_listrik_signature_' + i" :name="'isolasi_listrik['+i+'][signature]'" :value="row.signature">
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
    <button type="button" @click="addListrik()" class="mt-2 text-xs bg-blue-500 text-white px-3 py-1 rounded">+ Tambah Baris</button>

    <!-- ISOLASI NON LISTRIK -->
    <h4 class="font-semibold mt-4">Isolasi Energi Non Listrik</h4>
    <table class="table-auto w-full text-sm border mt-2">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-2">Peralatan / Energi</th>
                <th class="border p-2">Jenis</th>
                <th class="border p-2">Tempat</th>
                <th class="border p-2">Locked, Tagged</th>
                <th class="border p-2">Tested</th>
                <th class="border p-2">TTD</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(row, i) in nonListrik" :key="i">
                <tr>
                    <td class="border p-1"><input type="text" :name="'isolasi_non_listrik['+i+'][peralatan]'" x-model="row.peralatan" class="input w-full"></td>
                    <td class="border p-1"><input type="text" :name="'isolasi_non_listrik['+i+'][jenis]'" x-model="row.jenis" class="input w-full"></td>
                    <td class="border p-1"><input type="text" :name="'isolasi_non_listrik['+i+'][tempat]'" x-model="row.tempat" class="input w-full"></td>
                    <td class="border p-1"><input type="text" :name="'isolasi_non_listrik['+i+'][locked]'" x-model="row.locked" class="input w-full"></td>
                    <td class="border p-1"><input type="text" :name="'isolasi_non_listrik['+i+'][tested]'" x-model="row.tested" class="input w-full"></td>
                    <td class="border p-1 text-center">
                           <template x-if="row.signature">
        <img :src="row.signature" class="h-10 mx-auto mb-1">
    </template>
                        <button type="button" @click="openSignPad('isolasi_non_listrik_signature_' + i)" class="text-blue-600 underline text-xs">TTD</button>
                        <input type="hidden" :id="'isolasi_non_listrik_signature_' + i" :name="'isolasi_non_listrik['+i+'][signature]'" :value="row.signature">
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
    <button type="button" @click="addNonListrik()" class="mt-2 text-xs bg-blue-500 text-white px-3 py-1 rounded">+ Tambah Baris</button>
</div>

@php
    // Ambil data dari old input / database
    $rawPengukuranGas = old('pengukuran_gas') ?? $permit->pengukuran_gas ?? [];

    // Decode jika string JSON, atau langsung array
    if (is_string($rawPengukuranGas)) {
        $parsedGas = json_decode($rawPengukuranGas, true) ?? [];
    } else {
        $parsedGas = $rawPengukuranGas;
    }

    // Default structure untuk gas
    $gasKeys = ['O2 (19.5% - 23.5%)', 'LEL (< 5%)', 'CO (≤ 25ppm)', 'H2S (≤ 1ppm)', 'O3 (≤ 0.2ppm)'];
    foreach ($gasKeys as $key) {
        if (!isset($parsedGas[$key]) || !is_array($parsedGas[$key])) {
            $parsedGas[$key] = [];
        }
    }

    // Encode ke string JSON aman
    $jsonPengukuranGas = json_encode($parsedGas);
@endphp

<div
    x-data="{
        defaultKeys: ['O2 (19.5% - 23.5%)', 'LEL (< 5%)', 'CO (≤ 25ppm)', 'H2S (≤ 1ppm)', 'O3 (≤ 0.2ppm)'],
        rawData: JSON.parse(@js($jsonPengukuranGas)),
        dataGas: {},
        init() {
            this.defaultKeys.forEach(key => {
                this.dataGas[key] = this.rawData[key] ?? [];
            });
        },
        addRow(gas) {
            this.dataGas[gas].push({ tgl: '', hasil: '', jam: '', sign: '' });
        }
    }"
    x-init="init()"
    class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto text-sm mt-6"
>
    <h3 class="font-bold bg-black text-white px-2 py-1">
        3. Pengukuran Berkala Kadar Gas di Udara
        <span class="text-xs font-normal italic">(diisi oleh <strong>Permit Verificator</strong>)</span>
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
                        <th class="border px-2 py-1">TTD</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, index) in rows" :key="index">
                        <tr>
                            <td class="border px-1 py-1">
                                <input type="date" x-model="row.tgl" :name="'pengukuran_gas['+gas+']['+index+'][tgl]'" class="input w-full text-xs">
                            </td>
                            <td class="border px-1 py-1">
                                <input type="text" x-model="row.hasil" :name="'pengukuran_gas['+gas+']['+index+'][hasil]'" class="input w-full text-xs">
                            </td>
                            <td class="border px-1 py-1">
                                <input type="time" x-model="row.jam" :name="'pengukuran_gas['+gas+']['+index+'][jam]'" class="input w-full text-xs">
                            </td>
                            <td class="border px-1 py-1 text-center">
                                <template x-if="row.sign">
    <img :src="row.sign" class="h-10 mx-auto mb-1">
</template>

<button type="button" @click="openSignPad('pengukuran_gas_signature_' + gas + '_' + index)" class="text-blue-600 underline text-xs">TTD</button>
<input type="hidden" :id="'pengukuran_gas_signature_' + gas + '_' + index" :name="'pengukuran_gas['+gas+']['+index+'][sign]'" x-model="row.sign">

                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <button type="button" @click="addRow(gas)" class="mt-2 bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">
                + Tambah Baris
            </button>
        </div>
    </template>
</div>
@php
    $syaratList = [
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

    $syaratSelected = old('syarat_ruang_tertutup') ?? $permit->syarat_ruang_tertutup ?? [];
    $syaratSelected = is_string($syaratSelected) ? json_decode($syaratSelected, true) : $syaratSelected;
@endphp

<div x-data="{
    list: @js($syaratList),
    selected: @js($syaratSelected),
    getVal(i) { return this.selected[i] ?? '' }
}" class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto text-sm mt-6">

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
            <template x-for="(text, index) in list" :key="index">
                <tr>
                    <td class="border px-2 py-1" x-html="text"></td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio"
                            :name="'syarat_ruang_tertutup['+index+']'"
                            value="ya"
                            :checked="getVal(index) === 'ya'">
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio"
                            :name="'syarat_ruang_tertutup['+index+']'"
                            value="na"
                            :checked="getVal(index) === 'na'">
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
</div>

{{-- BAGIAN 5 --}}
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        5. Rekomendasi Persyaratan Kerja Aman Tambahan dari 
        <em>Permit Verificator/Permit Issuer</em> 
        <span class="text-gray-300 text-xs">(jika ada)</span>
    </h3>

    <table class="table-auto w-full text-sm border mt-2">
        <tr>
            <td class="border px-2 py-2 align-top">
                <textarea name="rekomendasi_tambahan" class="w-full h-32 border rounded p-2 resize-none"
                    placeholder="Tulis rekomendasi jika ada...">{{ old('rekomendasi_tambahan', $permit->rekomendasi_tambahan ?? '') }}</textarea>
            </td>
            <td class="border text-center align-top px-4 py-2 w-24">
                <label class="block mb-1 font-medium">Ya</label>
                <input type="radio" name="rekomendasi_status" value="ya"
                    {{ old('rekomendasi_status', $permit->rekomendasi_status ?? '') === 'ya' ? 'checked' : '' }}>
                <label class="block mb-1 font-medium mt-3">N/A</label>
                <input type="radio" name="rekomendasi_status" value="na"
                    {{ old('rekomendasi_status', $permit->rekomendasi_status ?? '') === 'na' ? 'checked' : '' }}>
            </td>
        </tr>
    </table>
</div>

{{-- BAGIAN 6 --}}
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
                <th class="border px-2 py-1 text-center w-1/4">Tanda Tangan:</th>
                <th class="border px-2 py-1 text-center w-1/4">Tanggal:</th>
                <th class="border px-2 py-1 text-center w-1/4">Jam:</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                {{-- Nama --}}
                <td class="border px-2 py-2 text-center">
                    <input type="text" name="permit_requestor_name" class="input w-full text-center"
                        value="{{ old('permit_requestor_name', $permit->permit_requestor_name ?? '') }}">
                </td>

                {{-- Tanda Tangan --}}
                <td class="border px-2 py-2 text-center">
                    <button type="button"
                        @click="openSignPad('ruangtertutup_signature_permit_requestor')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>

                    @php
                        $signature = old('signature_permit_requestor', $permit->signature_permit_requestor ?? null);
                    @endphp

                    <input type="hidden" id="ruangtertutup_signature_permit_requestor" name="signature_permit_requestor"
                        value="{{ $signature }}">

                    @if ($signature)
                        <img src="{{ asset($signature) }}" class="h-12 mx-auto mt-1">
                    @endif
                </td>

                {{-- Tanggal --}}
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="permit_requestor_date" class="input w-full text-center"
                        value="{{ old('permit_requestor_date', $permit->permit_requestor_date ?? '') }}">
                </td>

                {{-- Jam --}}
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_requestor_time" class="input w-full text-center"
                        value="{{ old('permit_requestor_time', $permit->permit_requestor_time ?? '') }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>


{{-- BAGIAN 7: Verifikasi --}}
@php
    $issuerSign = old('signature_permit_issuer', optional($permit)->signature_permit_issuer);
@endphp
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
                    <input type="text" name="confined_verificator_name" class="input w-full text-center"
                        value="{{ old('confined_verificator_name', $permit->confined_verificator_name ?? '') }}">
                </td>
                <td class="border px-2 py-2 text-center">
                  <button type="button"
    @click="openSignPad('ruangtertutup_signature_confined_verificator')"
    class="text-blue-600 underline text-xs">
    Tanda Tangan
</button>
<input type="hidden" id="ruangtertutup_signature_confined_verificator" name="signature_confined_verificator"
    value="{{ old('signature_confined_verificator', $permit->signature_confined_verificator ?? '') }}">
@if(old('signature_confined_verificator', $permit->signature_confined_verificator ?? null))
    <img src="{{ asset(old('signature_confined_verificator', $permit->signature_confined_verificator)) }}" class="h-12 mx-auto mt-1">
@endif


                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="confined_verificator_date" class="input w-full text-center"
                        value="{{ old('confined_verificator_date', $permit->confined_verificator_date ?? '') }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="confined_verificator_time" class="input w-full text-center"
                        value="{{ old('confined_verificator_time', $permit->confined_verificator_time ?? '') }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">8. Penerbitan Izin Kerja</h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="text-sm">
            <strong><em>Permit Issuer:</em></strong><br>
            Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan/atau rekomendasi persyaratan kerja aman tambahan dari <em>Permit Issuer</em> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
        </p>
    </div>

    <table class="table-auto min-w-full text-sm border mt-3">
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
                    <input type="text" name="permit_issuer_name" class="input w-full text-center"
                        value="{{ old('permit_issuer_name', $permit->permit_issuer_name ?? '') }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <button type="button"
    @click="openSignPad('ruangtertutup_signature_permit_issuer')"
    class="text-blue-600 underline text-xs">
    Tanda Tangan
</button>
<input type="hidden" id="ruangtertutup_signature_permit_issuer" name="signature_permit_issuer"
    value="{{ old('signature_permit_issuer', $permit->signature_permit_issuer ?? '') }}">
@if(old('signature_permit_issuer', $permit->signature_permit_issuer ?? null))
    <img src="{{ asset(old('signature_permit_issuer', $permit->signature_permit_issuer)) }}" class="h-12 mx-auto mt-1">
@endif


                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="permit_issuer_date" class="input w-full text-center"
                        value="{{ old('permit_issuer_date', $permit->permit_issuer_date ?? '') }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_issuer_time" class="input w-full text-center"
                        value="{{ old('permit_issuer_time', $permit->permit_issuer_time ?? '') }}">
                </td>
            </tr>
        </tbody>
    </table>

    <div class="mt-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
            <div class="flex items-center gap-2">
                <label class="whitespace-nowrap font-medium">Dari Tanggal:</label>
                <input type="date" name="izin_berlaku_dari" class="input text-xs w-full"
                    value="{{ old('izin_berlaku_dari', $permit->izin_berlaku_dari ?? '') }}">
                <label class="whitespace-nowrap font-medium">Jam:</label>
                <input type="time" name="izin_berlaku_jam_dari" class="input text-xs w-28"
                    value="{{ old('izin_berlaku_jam_dari', $permit->izin_berlaku_jam_dari ?? '') }}">
            </div>
            <div class="flex items-center gap-2">
                <label class="whitespace-nowrap font-medium">Sampai Tanggal:</label>
                <input type="date" name="izin_berlaku_sampai" class="input text-xs w-full"
                    value="{{ old('izin_berlaku_sampai', $permit->izin_berlaku_sampai ?? '') }}">
                <label class="whitespace-nowrap font-medium">Jam:</label>
                <input type="time" name="izin_berlaku_jam_sampai" class="input text-xs w-28"
                    value="{{ old('izin_berlaku_jam_sampai', $permit->izin_berlaku_jam_sampai ?? '') }}">
            </div>
        </div>
    </div>
</div>

{{-- BAGIAN 9: Pengesahan --}}
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
                    <input type="text" name="permit_authorizer_name" class="input w-full text-center"
                        value="{{ old('permit_authorizer_name', $permit->permit_authorizer_name ?? '') }}">
                </td>
              <td class="border px-2 py-2 text-center">
    <button 
        type="button"
        @click="openSignPad('ruangtertutup_signature_permit_authorizer')"
        class="text-blue-600 underline text-xs">
        Tanda Tangan
    </button>
    <input type="hidden" id="ruangtertutup_signature_permit_authorizer" name="signature_permit_authorizer"
        value="{{ old('signature_permit_authorizer', $permit->signature_permit_authorizer ?? '') }}">
    
@if(old('signature_permit_authorizer', $permit->signature_permit_authorizer ?? null))
    <img src="{{ asset(old('signature_permit_authorizer', $permit->signature_permit_authorizer)) }}" class="h-12 mx-auto mt-1">
@endif

</td>

                <td class="border px-2 py-2 text-center">
                    <input type="date" name="permit_authorizer_date" class="input w-full text-center"
                        value="{{ old('permit_authorizer_date', $permit->permit_authorizer_date ?? '') }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_authorizer_time" class="input w-full text-center"
                        value="{{ old('permit_authorizer_time', $permit->permit_authorizer_time ?? '') }}">
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
                    <input type="text" name="permit_receiver_name" class="input w-full text-center"
                        value="{{ old('permit_receiver_name', $permit->permit_receiver_name ?? '') }}">
               <td class="border px-2 py-2 text-center">
    <button 
        type="button"
        @click="openSignPad('ruangtertutup_signature_permit_receiver')"
        class="text-blue-600 underline text-xs">
        Tanda Tangan
    </button>
    <input type="hidden" id="ruangtertutup_signature_permit_receiver" name="signature_permit_receiver"
        value="{{ old('signature_permit_receiver', $permit->signature_permit_receiver ?? '') }}">

@if(old('signature_permit_receiver', $permit->signature_permit_receiver ?? null))
    <img src="{{ asset(old('signature_permit_receiver', $permit->signature_permit_receiver)) }}" class="h-12 mx-auto mt-1">
@endif

</td>

                <td class="border px-2 py-2 text-center">
                    <input type="date" name="permit_receiver_date" class="input w-full text-center"
                        value="{{ old('permit_receiver_date', $permit->permit_receiver_date ?? '') }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_receiver_time" class="input w-full text-center"
                        value="{{ old('permit_receiver_time', $permit->permit_receiver_time ?? '') }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>
@php
    $defaultPekerja = [['nama'=>'', 'perusahaan'=>'', 'tanggal'=>'', 'masuk'=>'', 'keluar'=>'', 'sign'=>'']];
    $rawPekerja = old('pekerja_masuk_keluar') ?? $permit->pekerja_masuk_keluar ?? $defaultPekerja;
    $pekerjaList = is_string($rawPekerja) ? json_decode($rawPekerja, true) : $rawPekerja;
    if (!is_array($pekerjaList) || count($pekerjaList) === 0) {
        $pekerjaList = $defaultPekerja;
    }
@endphp

<div
    x-data="{
        pekerjaList: {{ Js::from($pekerjaList) }},
        addRow() {
            this.pekerjaList.push({
                nama: '', perusahaan: '', tanggal: '', masuk: '', keluar: '', sign: ''
            });
        }
    }"
    class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm"
>
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
                        <input type="text" :name="'pekerja_masuk_keluar['+index+'][nama]'" x-model="pekerja.nama" class="input w-full text-xs">
                    </td>
                    <td class="border px-1 py-1">
                        <input type="text" :name="'pekerja_masuk_keluar['+index+'][perusahaan]'" x-model="pekerja.perusahaan" class="input w-full text-xs">
                    </td>
                    <td class="border px-1 py-1">
                        <input type="date" :name="'pekerja_masuk_keluar['+index+'][tanggal]'" x-model="pekerja.tanggal" class="input w-full text-xs">
                    </td>
                    <td class="border px-1 py-1">
                        <input type="time" :name="'pekerja_masuk_keluar['+index+'][masuk]'" x-model="pekerja.masuk" class="input w-full text-xs">
                    </td>
                    <td class="border px-1 py-1">
                        <input type="time" :name="'pekerja_masuk_keluar['+index+'][keluar]'" x-model="pekerja.keluar" class="input w-full text-xs">
                    </td>
                   <td class="border px-1 py-1 text-center">
    <button type="button"
        @click="openSignPad('pekerja_signature_' + index)"
        class="text-blue-600 underline text-xs">TTD</button>
    
    <input type="hidden"
        :id="'pekerja_signature_' + index"
        :name="'pekerja_masuk_keluar['+index+'][sign]'"
        x-model="pekerja.sign">

    <template x-if="pekerja.sign">
        <img :src="pekerja.sign" class="h-10 mx-auto mt-1">
    </template>
</td>

                </tr>
            </template>
        </tbody>
    </table>

    <div class="mt-3 text-right">
        <button @click="addRow()" type="button" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-4 py-1 rounded shadow">
            + Tambah Baris Pekerja
        </button>
    </div>
</div>

@php
    $defaultChecklist = [
        'Peralatan/permesinan yang akan dinyalakan kembali (re-energize) sudah diidentifikasi.',
        'Semua peralatan kerja sudah dipindahkan/diamankan dari peralatan / permesinan yang akan dinyalakan kembali.',
        'Semua orang yang dekat dengan area kerja sudah diinformasikan akan adanya peralatan/permesinan yang akan dinyalakan.',
        'Orang-orang yang tidak berkepentingan harus berada di luar area.',
        'Machine guarding pada peralatan/permesinan yang tidak mempengaruhi proses live testing sudah dipasang kembali.',
        'Semua lock dan tag sudah dilepaskan.',
        'Standby person telah ditunjuk untuk memastikan bahwa tidak ada orang berada di sekitar area dimana terdapat peralatan mesin tanpa machine guarding.',
        'Peralatan/permesinan sudah dinyalakan kembali.',
        'Setelah live testing, isolasi & penguncian harus kembali dipasang apabila pekerjaan belum selesai.'
    ];

    $rawChecklist = old('live_testing_checklist') ?? $permit->live_testing_checklist ?? [];
    $selectedChecklist = is_string($rawChecklist) ? json_decode($rawChecklist, true) : $rawChecklist;
    if (!is_array($selectedChecklist)) $selectedChecklist = [];
@endphp

<div 
    x-data="{
        checklist: {{ Js::from($defaultChecklist) }},
        selected: {{ Js::from($selectedChecklist) }}
    }"
    class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm"
>
    <h3 class="font-bold bg-black text-white px-2 py-1">
        12. <em>Live Testing</em> 
        <span class="italic text-xs font-normal">(Jika ada dan diisi oleh <strong>Isolation Officer</strong>)</span>
    </h3>

    <p class="text-xs italic mt-1 mb-2 text-gray-600">
        Jika peralatan/permesinan harus dinyalakan kembali (live testing), hal-hal berikut harus dilengkapi...
    </p>

    <table class="table-auto w-full border text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-left">Checklist</th>
                <th class="border px-2 py-1 w-20 text-center">(Centang)</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(item, index) in checklist" :key="index">
                <tr>
                    <td class="border px-2 py-1" x-text="item"></td>
                    <td class="border px-2 py-1 text-center">
                        <input 
                            type="checkbox"
                            :name="'live_testing_checklist[' + index + ']'"
                            value="ya"
                            x-bind:checked="selected[index] === 'ya'"
                            @change="selected[index] = $event.target.checked ? 'ya' : null"
                        >
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
{{-- Nama, TTD, Tanggal, Jam --}}
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
                <input type="text" name="live_testing_name" class="input w-full text-center"
                    value="{{ old('live_testing_name', $permit->live_testing_name ?? '') }}">
            </td>
            <td class="border px-2 py-2 text-center">
    <button type="button"
        @click="openSignPad('ruangtertutup_signature_live_testing')"
        class="text-blue-600 underline text-xs">Tanda Tangan</button>
    <input type="hidden"
        id="ruangtertutup_signature_live_testing"
        name="live_testing_signature"
        value="{{ old('live_testing_signature', $permit->live_testing_signature ?? '') }}">
    
    @if (old('live_testing_signature', $permit->live_testing_signature ?? null))
        <img src="{{ asset(old('live_testing_signature', $permit->live_testing_signature)) }}" class="h-12 mx-auto mt-1">
    @endif
</td>

            <td class="border px-2 py-2 text-center">
                <input type="date" name="live_testing_date" class="input w-full text-center"
                    value="{{ old('live_testing_date', $permit->live_testing_date ?? '') }}">
            </td>
            <td class="border px-2 py-2 text-center">
                <input type="time" name="live_testing_time" class="input w-full text-center"
                    value="{{ old('live_testing_time', $permit->live_testing_time ?? '') }}">
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

<!-- Bagian 13: Penutupan Izin Kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">13. Penutupan Izin Kerja</h3>

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
<div class="flex justify-center gap-3 mt-8">
    <button type="button"
        onclick="if (confirm('Hapus semua tanda tangan?')) { document.getElementById('clear_all_signatures').value = '1'; this.closest('form').submit(); }"
        class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded shadow-md transition duration-200">
        Hapus Semua TTD
    </button>
    <button type="submit" name="action" value="save"
        class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-6 rounded shadow-md transition duration-200">
        ?? Simpan
    </button>
</div>
</form>
</x-app-layout>
