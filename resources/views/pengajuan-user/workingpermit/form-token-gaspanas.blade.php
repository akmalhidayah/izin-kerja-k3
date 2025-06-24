@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded relative mb-4 text-sm">
        {{ session('success') }}
    </div>
@endif

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-sm text-gray-800 dark:text-gray-200 leading-tight">
            WORKING PERMIT GAS PANAS
        </h2>
    </x-slot>

    <section class="bg-cover bg-center bg-no-repeat py-10 px-4" style="background-image: url('/images/bg-login.jpg');">
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-6">

  <form method="POST" action="{{ route('working-permit.gaspanas.token.store', $permit->token) }}">
    @csrf
<input type="hidden" name="notification_id" value="{{ $notification->id ?? '' }}">
    <!-- Bagian 1: Detail Pekerjaan -->
    <div class="text-center mb-4">
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
                <input type="text" name="lokasi_pekerjaan" class="input w-full text-sm"
                    value="{{ old('lokasi_pekerjaan', $detail?->location) }}">
            </div>
            <div>
                <label class="font-semibold">Tanggal:</label>
                <input type="date" name="tanggal_pekerjaan" class="input w-full text-sm"
                    value="{{ old('tanggal_pekerjaan', \Carbon\Carbon::parse($detail?->work_date)->format('Y-m-d')) }}">
            </div>
        </div>

        <div>
            <label class="font-semibold">Uraian pekerjaan:</label>
            <textarea name="uraian_pekerjaan" class="textarea w-full text-sm">{{ old('uraian_pekerjaan', $detail?->job_description) }}</textarea>
        </div>

        <div>
            <label class="font-semibold">Peralatan/perlengkapan yang akan digunakan pada pekerjaan:</label>
            <textarea name="peralatan_digunakan" class="textarea w-full text-sm">{{ old('peralatan_digunakan', $detail?->equipment) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="font-semibold">Perkiraan jumlah pekerja yang akan terlibat dalam pekerjaan ini:</label>
                <input type="number" name="jumlah_pekerja" class="input w-full text-sm"
                    value="{{ old('jumlah_pekerja', $detail?->worker_count) }}">
            </div>
            <div>
                <label class="font-semibold">Nomor gawat darurat yang harus dihubungi saat darurat:</label>
                <input type="text" name="nomor_darurat" class="input w-full text-sm"
                    value="{{ old('nomor_darurat', $detail?->emergency_contact) }}">
            </div>
        </div>
    </div>
@php
    $daftarPekerja = old('daftar_pekerja', json_decode($permit?->daftar_pekerja ?? '[]', true));
@endphp

<div 
    x-data="{ daftar_pekerja: [] }" 
    x-init="daftar_pekerja = {{ json_encode($daftarPekerja ?: [['nama' => '', 'signature' => '']]) }}">
    
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
            <template x-for="(item, index) in daftar_pekerja" :key="index">
                <tr>
                    <td class="border px-2 py-1">
                        <input type="text" :name="'daftar_pekerja[' + index + '][nama]'" x-model="item.nama" class="input w-full text-sm">
                    </td>
                   <td class="border px-2 py-1 text-center">
    <button 
        type="button"
        class="text-blue-600 underline text-xs"
        @click="openSignPad('daftar_pekerja_' + index + '_signature')">
        Tanda Tangan
    </button>

    <input type="hidden" 
        :id="'daftar_pekerja_' + index + '_signature'" 
        :name="'daftar_pekerja[' + index + '][signature]'" 
        x-model="item.signature">

    <template x-if="item.signature">
        <img :src="item.signature" class="mx-auto mt-1 h-10 border rounded shadow" />
    </template>
</td>

                </tr>
            </template>
        </tbody>
    </table>

    <div class="mt-3">
        <button 
            type="button" 
            @click="daftar_pekerja.push({ nama: '', signature: '' })"
            class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">
            + Tambah Baris
        </button>
    </div>

    {{-- Upload Sketsa Pekerjaan --}}
    <div class="mt-5">
        <label class="block text-sm font-semibold mb-1">Sketsa Pekerjaan (Opsional)</label>
        <input 
            type="file" 
            name="sketsa_pekerjaan" 
            accept="image/*" 
            class="text-sm file-input file-input-bordered w-full max-w-md" />

        @if (!empty($permit?->sketsa_pekerjaan))
            <div class="mt-3">
                <span class="text-xs text-gray-500 italic">Sketsa yang sudah diupload:</span><br>
                <img src="{{ asset($permit->sketsa_pekerjaan) }}" alt="Sketsa Pekerjaan" class="mt-2 border max-w-full h-auto shadow" style="max-height: 300px;">
            </div>
        @endif
    </div>
</div>

<!-- Bagian 3: Persyaratan Kerja Aman -->
@php
    $checklist = old('syarat', json_decode($permit?->checklist_kerja_aman ?? '[]', true));
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
            @foreach ($syaratKerja as $i => $item)
                <tr>
                    <td class="border px-2 py-1">{!! $item !!}</td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="syarat[{{ $i }}]" value="ya" {{ ($checklist[$i] ?? '') === 'ya' ? 'checked' : '' }}>
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="syarat[{{ $i }}]" value="na" {{ ($checklist[$i] ?? '') === 'na' ? 'checked' : '' }}>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Bagian 4: Rekomendasi Persyaratan Kerja Aman Tambahan -->
@php
    $rekomendasiTambahan = old('rekomendasi_tambahan', $permit?->rekomendasi_tambahan ?? '');
    $rekomendasiStatus = old('rekomendasi_status', $permit?->rekomendasi_status ?? '');
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        4. Rekomendasi Persyaratan Kerja Aman Tambahan dari 
        <em>Permit Verificator/Permit Issuer</em> 
        <span class="text-gray-300 text-xs">(jika ada)</span>
    </h3>

    <table class="table-auto w-full text-sm border mt-2">
        <tr>
            <td class="border px-2 py-2 align-top">
                <textarea name="rekomendasi_tambahan" class="w-full h-32 border rounded p-2 resize-none" placeholder="Tulis rekomendasi jika ada...">{{ $rekomendasiTambahan }}</textarea>
            </td>
            <td class="border text-center align-top px-4 py-2 w-24">
                <label class="block mb-1 font-medium">Ya</label>
                <input type="radio" name="rekomendasi_status" value="ya" {{ $rekomendasiStatus === 'ya' ? 'checked' : '' }}>
            </td>
        </tr>
    </table>
</div>

<!-- Bagian 5: Permohonan Izin Kerja -->
@php
    $reqName = old('permit_requestor_name', $permit?->permit_requestor_name ?? '');
    $reqSign = old('permit_requestor_sign', $permit?->permit_requestor_sign ?? '');
    $reqDateRaw = old('permit_requestor_date', $permit?->permit_requestor_date ?? '');
    $reqTimeRaw = old('permit_requestor_time', $permit?->permit_requestor_time ?? '');

    $reqDate = $reqDateRaw ? \Carbon\Carbon::parse($reqDateRaw)->format('Y-m-d') : '';
    $reqTime = $reqTimeRaw ? \Carbon\Carbon::parse($reqTimeRaw)->format('H:i') : '';
@endphp

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
                    <input type="text" name="permit_requestor_name" class="input w-full text-center" value="{{ $reqName }}">
                </td>

                <td class="border px-2 py-2 text-center">
                    @if ($reqSign && file_exists(public_path($reqSign)))
                        <img src="{{ asset($reqSign) }}" alt="Tanda Tangan" class="h-16 mx-auto mb-1 border">
                    @endif

                    <button 
                        type="button"
                        onclick="openSignPad('gaspanas_signature_permit_requestor')"
                        class="text-blue-600 underline text-xs mt-1">
                        {{ $reqSign ? 'Ubah Tanda Tangan' : 'Tanda Tangan' }}
                    </button>

                    <input 
                        type="hidden" 
                        name="permit_requestor_sign" 
                        id="gaspanas_signature_permit_requestor" 
                        value="{{ $reqSign }}">
                </td>

                <td class="border px-2 py-2 text-center">
                    <input type="date" name="permit_requestor_date" class="input w-full text-center" value="{{ $reqDate }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_requestor_time" class="input w-full text-center" value="{{ $reqTime }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>


@php
    $verifiedWorkers = old('verified_workers', json_decode($permit?->verified_workers ?? '[]', true));
    $verificatorName = old('verificator_name', $permit?->verificator_name ?? '');
    $verificatorSign = old('signature_verificator', $permit?->verificator_sign ?? '');
    $verificatorDateRaw = old('verificator_date', $permit?->verificator_date);
    $verificatorTimeRaw = old('verificator_time', $permit?->verificator_time);
    $verificatorDate = $verificatorDateRaw ? \Carbon\Carbon::parse($verificatorDateRaw)->format('Y-m-d') : '';
    $verificatorTime = $verificatorTimeRaw ? \Carbon\Carbon::parse($verificatorTimeRaw)->format('H:i') : '';
@endphp

<div x-data="{ diverifikasi: {{ json_encode($verifiedWorkers ?: [['nama' => '']]) }} }" class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">6. Verifikasi Izin Kerja</h3>
    
    <div class="p-2 border-t-0 border border-gray-300 text-sm">
        <p class="italic font-semibold mb-1">Hot Material/Gasses Permit Verificator:</p>
        <p>
            Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman serta rekomendasi tambahan dari 
            <em>Permit Issuer</em> telah dipenuhi. <strong>Berikut nama-nama pekerja yang diizinkan:</strong>
        </p>
    </div>

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100 text-center">
            <tr><th class="border px-2 py-1">Nama</th></tr>
        </thead>
        <tbody>
            <template x-for="(row, index) in diverifikasi" :key="index">
                <tr>
                    <td class="border px-2 py-1">
                        <input type="text" :name="'verified_workers[' + index + '][nama]'" x-model="row.nama" class="input w-full text-sm">
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
    
    <div class="mt-3">
        <button type="button" @click="diverifikasi.push({ nama: '' })" class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">
            + Tambah Baris
        </button>
    </div>

    <table class="table-auto w-full text-sm border mt-6">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center">Nama</th>
                <th class="border px-2 py-1 text-center">Tanda Tangan</th>
                <th class="border px-2 py-1 text-center">Tanggal</th>
                <th class="border px-2 py-1 text-center">Jam</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border text-center px-2 py-2">
                    <input type="text" name="verificator_name" class="input w-full text-center" value="{{ $verificatorName }}">
                </td>
                <td class="border text-center px-2 py-2">
                    @if ($verificatorSign && file_exists(public_path($verificatorSign)))
                        <img src="{{ asset($verificatorSign) }}" alt="Tanda Tangan" class="h-16 mx-auto mb-1 border">
                    @endif

                    <button 
                        type="button" 
                        onclick="openSignPad('gaspanas_signature_verificator')" 
                        class="text-blue-600 underline text-xs mt-1">
                        {{ $verificatorSign ? 'Ubah Tanda Tangan' : 'Tanda Tangan' }}
                    </button>

                    <input type="hidden" name="signature_verificator" id="gaspanas_signature_verificator" value="{{ $verificatorSign }}">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="date" name="verificator_date" class="input w-full text-center" value="{{ $verificatorDate }}">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="time" name="verificator_time" class="input w-full text-center" value="{{ $verificatorTime }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>

@php
    $issuerName = old('permit_issuer_name', $permit?->permit_issuer_name ?? '');
    $issuerSign = old('signature_permit_issuer', $permit?->permit_issuer_sign ?? '');
    $issuerDateRaw = old('permit_issuer_date', $permit?->permit_issuer_date);
    $issuerTimeRaw = old('permit_issuer_time', $permit?->permit_issuer_time);
   $issuerDate = $issuerDateRaw ? \Carbon\Carbon::parse($issuerDateRaw)->format('Y-m-d') : '';
$issuerTime = $issuerTimeRaw ? \Carbon\Carbon::parse($issuerTimeRaw)->format('H:i') : '';
    $izinDariDate = old('izin_berlaku_dari', $permit?->izin_berlaku_dari);
    $izinDariTime = old('izin_berlaku_jam_dari', $permit?->izin_berlaku_jam_dari);
    $izinSampaiDate = old('izin_berlaku_sampai', $permit?->izin_berlaku_sampai);
    $izinSampaiTime = old('izin_berlaku_jam_sampai', $permit?->izin_berlaku_jam_sampai);
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">7. Penerbitan Izin Kerja</h3>

    <div class="border border-t-0 border-gray-300 p-3 text-sm">
        <strong><em>Permit Issuer:</em></strong><br>
        Saya menyatakan bahwa semua persyaratan kerja aman dan/atau rekomendasi tambahan telah dipenuhi.
    </div>

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center">Nama</th>
                <th class="border px-2 py-1 text-center">Tanda Tangan</th>
                <th class="border px-2 py-1 text-center">Tanggal</th>
                <th class="border px-2 py-1 text-center">Jam</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-2 text-center">
                    <input type="text" name="permit_issuer_name" class="input w-full text-center" value="{{ $issuerName }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    @if ($issuerSign && file_exists(public_path($issuerSign)))
                        <img src="{{ asset($issuerSign) }}" alt="Tanda Tangan" class="h-16 mx-auto mb-1 border">
                    @endif

                    <button 
                        type="button" 
                        onclick="openSignPad('gaspanas_signature_permit_issuer')" 
                        class="text-blue-600 underline text-xs mt-1">
                        {{ $issuerSign ? 'Ubah Tanda Tangan' : 'Tanda Tangan' }}
                    </button>

                    <input type="hidden" name="signature_permit_issuer" id="gaspanas_signature_permit_issuer" value="{{ $issuerSign }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="permit_issuer_date" class="input w-full text-center" value="{{ $issuerDate }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_issuer_time" class="input w-full text-center" value="{{ $issuerTime }}">
                </td>
            </tr>
        </tbody>
    </table>

    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
        <div class="flex items-center gap-2">
            <label class="font-medium whitespace-nowrap">Dari Tanggal:</label>
            <input type="date" name="izin_berlaku_dari" class="input w-full text-xs" value="{{ $izinDariDate }}">
            <label class="font-medium">Jam:</label>
            <input type="time" name="izin_berlaku_jam_dari" class="input text-xs w-28" value="{{ $izinDariTime }}">
        </div>
        <div class="flex items-center gap-2">
            <label class="font-medium whitespace-nowrap">Sampai Tanggal:</label>
            <input type="date" name="izin_berlaku_sampai" class="input w-full text-xs" value="{{ $izinSampaiDate }}">
            <label class="font-medium">Jam:</label>
            <input type="time" name="izin_berlaku_jam_sampai" class="input text-xs w-28" value="{{ $izinSampaiTime }}">
        </div>
    </div>
</div>

@php
    $authorizerName = old('permit_authorizer_name', $permit?->permit_authorizer_name ?? '');
    $authorizerSign = old('signature_permit_authorizer', $permit?->permit_authorizer_sign ?? '');
    $authorizerDateRaw = old('permit_authorizer_date', $permit?->permit_authorizer_date);
    $authorizerTimeRaw = old('permit_authorizer_time', $permit?->permit_authorizer_time);
 $authorizerDate = $authorizerDateRaw ? \Carbon\Carbon::parse($authorizerDateRaw)->format('Y-m-d') : '';
$authorizerTime = $authorizerTimeRaw ? \Carbon\Carbon::parse($authorizerTimeRaw)->format('H:i') : '';

@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">8. Pengesahan Izin Kerja</h3>

    <div class="border border-t-0 border-gray-300 p-3 text-sm">
        <strong><em>Permit Authorizer:</em></strong><br>
        Saya menyatakan bahwa semua persyaratan kerja aman dan rekomendasi tambahan telah dipenuhi. Saya sudah menekankan <em>major hazards</em> dan pengendaliannya kepada <em>Permit Receiver</em>.
    </div>

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center w-1/4">Nama</th>
                <th class="border px-2 py-1 text-center w-1/4">Tanda Tangan</th>
                <th class="border px-2 py-1 text-center w-1/4">Tanggal</th>
                <th class="border px-2 py-1 text-center w-1/4">Jam</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-2 text-center">
                    <input type="text" name="permit_authorizer_name" class="input w-full text-center" value="{{ $authorizerName }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    @if ($authorizerSign && file_exists(public_path($authorizerSign)))
                        <img src="{{ asset($authorizerSign) }}" alt="Tanda Tangan" class="h-16 mx-auto mb-1 border">
                    @endif

                    <button 
                        type="button" 
                        onclick="openSignPad('gaspanas_signature_permit_authorizer')" 
                        class="text-blue-600 underline text-xs mt-1">
                        {{ $authorizerSign ? 'Ubah Tanda Tangan' : 'Tanda Tangan' }}
                    </button>

                    <input type="hidden" name="signature_permit_authorizer" id="gaspanas_signature_permit_authorizer" value="{{ $authorizerSign }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="permit_authorizer_date" class="input w-full text-center" value="{{ $authorizerDate }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_authorizer_time" class="input w-full text-center" value="{{ $authorizerTime }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>

@php
    $receiverName = old('permit_receiver_name', $permit?->permit_receiver_name ?? '');
    $receiverSign = old('signature_permit_receiver', $permit?->permit_receiver_sign ?? '');
    $receiverDateRaw = old('permit_receiver_date', $permit?->permit_receiver_date);
    $receiverTimeRaw = old('permit_receiver_time', $permit?->permit_receiver_time);
  $receiverDate = $receiverDateRaw ? \Carbon\Carbon::parse($receiverDateRaw)->format('Y-m-d') : '';
$receiverTime = $receiverTimeRaw ? \Carbon\Carbon::parse($receiverTimeRaw)->format('H:i') : '';

@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">9. Pelaksanaan Pekerjaan</h3>

    <div class="border border-t-0 border-gray-300 p-3 text-sm">
        <strong><em>Permit Receiver:</em></strong><br>
        Saya menyatakan bahwa semua persyaratan kerja aman dan rekomendasi tambahan telah dipenuhi. Saya sudah mensosialisasikan <em>major hazards</em> dan pengendaliannya kepada semua pekerja.
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
                    <input type="text" name="permit_receiver_name" class="input w-full text-center" value="{{ $receiverName }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    @if ($receiverSign && file_exists(public_path($receiverSign)))
                        <img src="{{ asset($receiverSign) }}" alt="Tanda Tangan" class="h-16 mx-auto mb-1 border">
                    @endif

                    <button 
                        type="button" 
                        onclick="openSignPad('gaspanas_signature_permit_receiver')" 
                        class="text-blue-600 underline text-xs mt-1">
                        {{ $receiverSign ? 'Ubah Tanda Tangan' : 'Tanda Tangan' }}
                    </button>

                    <input type="hidden" name="signature_permit_receiver" id="gaspanas_signature_permit_receiver" value="{{ $receiverSign }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="permit_receiver_date" class="input w-full text-center" value="{{ $receiverDate }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_receiver_time" class="input w-full text-center" value="{{ $receiverTime }}">
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

<!-- Bagian 10: Penutupan Izin Kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">10. Penutupan Izin Kerja</h3>

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
</x-app-layout>