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
<form method="POST" action="{{ route('working-permit.ketinggian.token.store', $permit->token) }}" enctype="multipart/form-data">
    @csrf
        <input type="hidden" name="notification_id" value="{{ $notification->id ?? '' }}">


  <!-- Bagian 1: Detail Pekerjaan -->
       <div class="text-center mb-4">
        <h2 class="text-2xl font-bold uppercase">IZIN KERJA</h2>
        <h3 class="text-xl font-semibold text-gray-700">BEKERJA DI KETINGGIAN</h3>
        <p class="text-sm mt-2 text-gray-600">
            Izin kerja ini diberikan untuk pekerjaan di ketinggian yang memiliki risiko terjatuh dari ketinggian ≥ 1.8 m, penggunaan man basket/man box dengan mobile crane/hoist winch atau yang sejenis untuk mengangkat orang serta penggunaan gondola. Pekerjaan tidak bisa dimulai hingga izin kerja di verifikasi oleh Permit Verificator, diterbitkan oleh Permit Issuer, disahkan oleh Permit Authorizer dan major hazards & control disosialisasikan oleh Permit Receiver.
        </p>
    </div>

    <div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
        <h3 class="font-bold bg-black text-white px-2 py-1">1. Detail Pekerjaan</h3>
        <table class="table-auto w-full border text-sm">
            <tr>
                <td class="border font-semibold px-2 py-1 w-1/2">Lokasi pekerjaan:</td>
                <td class="border px-2 py-1 w-1/2">
                    <input type="text" name="lokasi_pekerjaan" class="input w-full text-xs"
                        value="{{ old('lokasi_pekerjaan', $detail?->location) }}">
                </td>
            </tr>
            <tr>
                <td class="border font-semibold px-2 py-1">Tanggal:</td>
                <td class="border px-2 py-1">
                    <input type="date" name="tanggal_pekerjaan" class="input w-full text-xs"
                        value="{{ old('tanggal_pekerjaan', \Carbon\Carbon::parse($detail?->work_date)->format('Y-m-d')) }}">
                </td>
            </tr>
            <tr>
                <td class="border font-semibold px-2 py-1" colspan="2">Uraian pekerjaan:</td>
            </tr>
            <tr>
                <td class="border px-2 py-1" colspan="2">
                    <textarea name="uraian_pekerjaan" rows="3" class="input w-full text-xs">{{ old('uraian_pekerjaan', $detail?->job_description) }}</textarea>
                </td>
            </tr>
            <tr>
                <td class="border font-semibold px-2 py-1" colspan="2">Peralatan/perlengkapan yang akan digunakan pada pekerjaan:</td>
            </tr>
            <tr>
                <td class="border px-2 py-1" colspan="2">
                    <textarea name="peralatan_digunakan" rows="2" class="input w-full text-xs">{{ old('peralatan_digunakan', $detail?->equipment) }}</textarea>
                </td>
            </tr>
            <tr>
                <td class="border font-semibold px-2 py-1" colspan="2">Perkiraan jumlah pekerja yang akan terlibat dalam pekerjaan ini:</td>
            </tr>
            <tr>
                <td class="border px-2 py-1" colspan="2">
                    <input type="number" name="jumlah_pekerja" class="input w-full text-xs"
                        value="{{ old('jumlah_pekerja', $detail?->worker_count) }}">
                </td>
            </tr>
            <tr>
                <td class="border font-semibold px-2 py-1" colspan="2">Nomor gawat darurat yang harus dihubungi saat darurat:</td>
            </tr>
            <tr>
                <td class="border px-2 py-1" colspan="2">
                    <input type="text" name="nomor_darurat" class="input w-full text-xs"
                        value="{{ old('nomor_darurat', $detail?->emergency_contact) }}">
                </td>
            </tr>
        </table>
    </div>
@php
$daftarPekerja = old('daftar_pekerja', $permit?->nama_pekerja ?? []);

@endphp

<div 
    x-data="{ daftar_pekerja: [] }" 
    x-init="daftar_pekerja = {{ json_encode($daftarPekerja ?: [['nama' => '', 'signature' => '']]) }}">

    <h3 class="font-bold bg-black text-white px-2 py-1">
        2. Daftar Pekerja dan Sketsa Pekerjaan
        <span class="font-normal text-xs italic">(bisa dalam lampiran terpisah)</span>
    </h3>

    <!-- Tabel Daftar Pekerja -->
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
                    <!-- Nama Pekerja -->
                    <td class="border px-2 py-1">
                        <input 
                            type="text" 
                            :name="`daftar_pekerja[${index}][nama]`" 
                            x-model="item.nama" 
                            class="input w-full text-sm" 
                            placeholder="Nama Pekerja">
                    </td>

                   <td class="border px-2 py-1 text-center">
    <button 
        type="button"
        class="text-blue-600 underline text-xs"
        @click="openSignPad(`ketinggian_pekerja_${index}_signature`)">
        Tanda Tangan
    </button>

    <input 
        type="hidden" 
        :id="`ketinggian_pekerja_${index}_signature`" 
        :name="`daftar_pekerja[${index}][signature]`" 
        x-model="item.signature">

    <template x-if="item.signature">
        <img 
            :src="item.signature" 
            class="mx-auto mt-1 h-10 border rounded shadow" />
    </template>
</td>

                </tr>
            </template>
        </tbody>
    </table>

    <!-- Tombol Tambah Baris -->
    <div class="mt-3">
        <button 
            type="button" 
            @click="daftar_pekerja.push({ nama: '', signature: '' })"
            class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">
            + Tambah Baris
        </button>
    </div>

    <!-- Upload Sketsa Pekerjaan -->
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
                <img 
                    src="{{ asset($permit->sketsa_pekerjaan) }}" 
                    alt="Sketsa Pekerjaan" 
                    class="mt-2 border max-w-full h-auto shadow" 
                    style="max-height: 300px;">
            </div>
        @endif
    </div>
</div>
{{-- 
  Pastikan di layout atau di bawah ini kamu punya:
  - Modal #signPadModal dengan canvas #signaturePad.
  - Fungsi global openSignPad(id), saveSignature(), clearSignature(), closeSignPad().
--}}

<!-- BAGIAN 3: Persyaratan Kerja Aman -->
@php
    $persyaratanKetinggian = [
        'Pekerja memiliki sertifikasi bekerja di ketinggian',
        'Telah dilakukan inspeksi peralatan keselamatan',
        'Lifeline atau fall arrest system tersedia dan sesuai',
        'Area kerja telah diberi pagar pengaman',
        'Perlengkapan APD lengkap (helm, sabuk pengaman, dll)',
        'Cuaca dalam kondisi aman untuk bekerja di ketinggian',
        'Supervisor melakukan briefing sebelum mulai kerja',
        'Sudah dilakukan uji coba tangga/scaffolding',
        'Komunikasi antar pekerja terjamin selama pekerjaan',
        'Area di bawah pekerjaan aman dan diberi tanda peringatan',
    ];

    // 👇 Tambahkan baris ini untuk mengambil nilai lama atau dari DB
    $kerjaAman = old('kerja_aman_ketinggian', $permit->kerja_aman_ketinggian ?? []);
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">3. Persyaratan Kerja Aman <span class="italic font-normal text-xs">(lingkari)</span></h3>
    <table class="table-auto w-full border text-sm mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-left">Persyaratan</th>
                <th class="border px-2 py-1 text-center w-16">Ya</th>
                <th class="border px-2 py-1 text-center w-16">N/A</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($persyaratanKetinggian as $index => $item)
                <tr>
                    <td class="border px-2 py-1">• {!! $item !!}</td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="kerja_aman_ketinggian[{{ $index }}]" value="ya"
                            {{ ($kerjaAman[$index] ?? '') === 'ya' ? 'checked' : '' }}>
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="kerja_aman_ketinggian[{{ $index }}]" value="na"
                            {{ ($kerjaAman[$index] ?? '') === 'na' ? 'checked' : '' }}>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Bagian 4 -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        4. Rekomendasi Persyaratan Kerja Aman Tambahan dari 
        <em>Permit Verificator/Permit Issuer</em> 
        <span class="text-xs font-normal italic">(jika ada)</span>
        <span class="float-right">(lingkari)</span>
    </h3>

    <table class="table-auto w-full border text-sm mt-2">
        <tbody>
            <tr>
                <td class="border px-2 py-6 w-full align-top">
                    <textarea name="rekomendasi_tambahan" rows="5"
                        class="w-full border text-sm p-2 resize-none"
                        placeholder="Tulis rekomendasi tambahan di sini jika ada...">{{ old('rekomendasi_tambahan', $permit->rekomendasi_tambahan ?? '') }}</textarea>
                </td>
                <td class="border px-2 py-1 text-center align-top" style="width: 80px;">
                    <label class="block">Ya</label>
                    <input type="radio" name="rekomendasi_ada" value="ya"
                        {{ old('rekomendasi_ada', $permit->rekomendasi_ada ?? '') == 'ya' ? 'checked' : '' }}>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@php
    $requestorName = old('permit_requestor_name', $permit?->permit_requestor_name);
    $requestorSign = old('signature_permit_requestor', $permit?->signature_permit_requestor);
@endphp
@php
    $requestorName = old('permit_requestor_name', $permit?->permit_requestor_name);
    $requestorSign = old('signature_permit_requestor', $permit?->signature_permit_requestor);
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">5. Permohonan Izin Kerja</h3>

    <div class="border border-t-0 border-gray-300 p-3 bg-gray-50">
        <p class="italic font-semibold">Permit Requestor:</p>
        <p>
            Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk dapat melakukan pekerjaan ini.
        </p>
    </div>

    <table class="table-auto w-full border mt-3 text-sm text-center">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1">Nama:</th>
                <th class="border px-2 py-1">Tanda tangan:</th>
                <th class="border px-2 py-1">Tanggal:</th>
                <th class="border px-2 py-1">Jam:</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-2">
                    <input type="text"
                        name="permit_requestor_name"
                        class="input w-full text-xs text-center"
                        value="{{ $requestorName }}"
                        placeholder="Nama">
                </td>
                <td class="border px-2 py-2">
                    @if ($requestorSign && file_exists(public_path($requestorSign)))
                        <img src="{{ asset($requestorSign) }}" class="h-16 mx-auto mb-1">
                    @endif
                    <button type="button"
                        onclick="openSignPad('ketinggian_signature_permit_requestor')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden"
                        id="ketinggian_signature_permit_requestor"
                        name="signature_permit_requestor"
                        value="{{ $requestorSign }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="date"
                        name="permit_requestor_date"
                        class="input w-full text-xs text-center"
                        value="{{ old('permit_requestor_date', $permit?->permit_requestor_date) }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="time"
                        name="permit_requestor_time"
                        class="input w-full text-xs text-center"
                        value="{{ old('permit_requestor_time', $permit?->permit_requestor_time) }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>

@php
    $rawWorkers = old('authorized_worker') ?? $permit?->authorized_workers ?? [];
    $authorizedWorkers = is_string($rawWorkers) ? json_decode($rawWorkers, true) : $rawWorkers;
    if (!is_array($authorizedWorkers)) $authorizedWorkers = [''];

    $verifikatorName = old('verifikator_name', $permit->verifikator_name ?? '');
    $verifikatorSign = old('signature_verifikator', $permit->signature_verifikator ?? '');
    $verifikatorDateRaw = old('verifikator_date', $permit->verifikator_date ?? null);
    $verifikatorTimeRaw = old('verifikator_time', $permit->verifikator_time ?? null);

    $verifikatorDate = $verifikatorDateRaw ? \Carbon\Carbon::parse($verifikatorDateRaw)->format('Y-m-d') : '';
    $verifikatorTime = $verifikatorTimeRaw ? \Carbon\Carbon::parse($verifikatorTimeRaw)->format('H:i') : '';
@endphp

<div x-data="{ workers: @js($authorizedWorkers) }" class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">6. Verifikasi Izin Kerja</h3>

    <div class="bg-gray-100 border border-t-0 border-gray-300 p-3">
        <p class="italic font-semibold">Working at Height Permit Verificator:</p>
        <p>
            Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman...
            <strong>Berikut nama-nama pekerja yang diizinkan:</strong>
        </p>
    </div>

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center">Nama Pekerja</th>
                <th class="border px-2 py-1 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(worker, index) in workers" :key="index">
                <tr>
                    <td class="border px-2 py-1">
                        <input type="text" :name="`authorized_worker[${index}]`" x-model="workers[index]" class="input w-full text-xs">
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <button type="button" @click="workers.splice(index, 1)" class="text-red-500 text-xs">Hapus</button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>

    <button type="button" @click="workers.push('')" class="mt-2 text-blue-600 text-xs">+ Tambah Pekerja</button>

    <table class="table-auto w-full border mt-4 text-sm">
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
                    <input type="text" name="verifikator_name" value="{{ $verifikatorName }}" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-1 text-center">
                    @if ($verifikatorSign && file_exists(public_path($verifikatorSign)))
                        <img src="{{ asset($verifikatorSign) }}" class="h-16 mx-auto mb-1">
                    @endif
                    <button type="button"
                        onclick="openSignPad('signature_verifikator')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden"
                        name="signature_verifikator"
                        id="signature_verifikator"
                        value="{{ $verifikatorSign }}">
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="date" name="verifikator_date" value="{{ $verifikatorDate }}" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="time" name="verifikator_time" value="{{ $verifikatorTime }}" class="input w-full text-xs text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>
@php
    $issuerName = old('permit_issuer_name', $permit->permit_issuer_name ?? '');
    $issuerSign = old('signature_permit_issuer', $permit->signature_permit_issuer ?? '');
    $issuerDate = old('permit_issuer_date', isset($permit->permit_issuer_date) ? \Carbon\Carbon::parse($permit->permit_issuer_date)->format('Y-m-d') : '');
    $issuerTime = old('permit_issuer_time', $permit->permit_issuer_time ?? '');
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">7. Penerbitan Izin Kerja</h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="italic font-semibold">Permit Issuer:</p>
        <p>Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman...</p>
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
                    <input type="text" name="permit_issuer_name" value="{{ $issuerName }}" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    @if ($issuerSign && file_exists(public_path($issuerSign)))
                        <img src="{{ asset($issuerSign) }}" class="h-16 mx-auto mb-1">
                    @endif
                    <button type="button" onclick="openSignPad('signature_permit_issuer_field')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    <input type="hidden" name="signature_permit_issuer" id="signature_permit_issuer_field" value="{{ $issuerSign }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="permit_issuer_date" value="{{ $issuerDate }}" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_issuer_time" value="{{ $issuerTime }}" class="input w-full text-xs text-center">
                </td>
            </tr>
        </tbody>
    </table>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 text-sm">
        <div class="flex flex-col">
            <label class="font-semibold mb-1">Izin kerja ini berlaku dari tanggal:</label>
            <input type="date" name="izin_berlaku_dari" value="{{ old('izin_berlaku_dari', isset($permit->izin_berlaku_dari) ? \Carbon\Carbon::parse($permit->izin_berlaku_dari)->format('Y-m-d') : '') }}" class="input text-xs">
        </div>
        <div class="flex flex-col">
            <label class="font-semibold mb-1">Jam mulai:</label>
            <input type="time" name="izin_berlaku_jam_dari" value="{{ old('izin_berlaku_jam_dari', $permit->izin_berlaku_jam_dari ?? '') }}" class="input text-xs">
        </div>
        <div class="flex flex-col">
            <label class="font-semibold mb-1">Sampai tanggal:</label>
            <input type="date" name="izin_berlaku_sampai" value="{{ old('izin_berlaku_sampai', isset($permit->izin_berlaku_sampai) ? \Carbon\Carbon::parse($permit->izin_berlaku_sampai)->format('Y-m-d') : '') }}" class="input text-xs">
        </div>
        <div class="flex flex-col">
            <label class="font-semibold mb-1">Jam selesai:</label>
            <input type="time" name="izin_berlaku_jam_sampai" value="{{ old('izin_berlaku_jam_sampai', $permit->izin_berlaku_jam_sampai ?? '') }}" class="input text-xs">
        </div>
    </div>
</div>

@php
    $authorizerName = old('permit_authorizer_name', $permit->permit_authorizer_name ?? '');
    $authorizerSign = old('signature_permit_authorizer', $permit->signature_permit_authorizer ?? '');
    $authorizerDate = old('permit_authorizer_date', $permit->permit_authorizer_date ?? '');
    $authorizerTime = old('permit_authorizer_time', $permit->permit_authorizer_time ?? '');
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">8. Pengesahan Izin Kerja</h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="italic font-semibold">Permit Authorizer:</p>
        <p>Saya menyatakan bahwa saya telah memeriksa area kerja...</p>
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
                    <input type="text" name="permit_authorizer_name" value="{{ $authorizerName }}" class="input w-full text-xs text-center">
                </td>
                <td class="border text-center px-2 py-2">
                    @if ($authorizerSign && file_exists(public_path($authorizerSign)))
                        <img src="{{ asset($authorizerSign) }}" class="h-16 mx-auto mb-1">
                    @endif
                    <button type="button" onclick="openSignPad('signature_permit_authorizer_field')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    <input type="hidden" name="signature_permit_authorizer" id="signature_permit_authorizer_field" value="{{ $authorizerSign }}">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="date" name="permit_authorizer_date" value="{{ $authorizerDate }}" class="input w-full text-xs text-center">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="time" name="permit_authorizer_time" value="{{ $authorizerTime }}" class="input w-full text-xs text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>

@php
    $receiverName = old('permit_receiver_name', $permit->permit_receiver_name ?? '');
    $receiverSign = old('signature_permit_receiver', $permit->signature_permit_receiver ?? '');
    $receiverDate = old('permit_receiver_date', $permit->permit_receiver_date ?? '');
    $receiverTime = old('permit_receiver_time', $permit->permit_receiver_time ?? '');
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">9. Pelaksanaan Pekerjaan</h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="italic font-semibold">Permit Receiver:</p>
        <p>Saya menyatakan bahwa semua persyaratan kerja aman...</p>
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
                    <input type="text" name="permit_receiver_name" value="{{ $receiverName }}" class="input w-full text-xs text-center">
                </td>
                <td class="border text-center px-2 py-2">
                    @if ($receiverSign && file_exists(public_path($receiverSign)))
                        <img src="{{ asset($receiverSign) }}" class="h-16 mx-auto mb-1">
                    @endif
                    <button type="button" onclick="openSignPad('signature_permit_receiver_field')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    <input type="hidden" name="signature_permit_receiver" id="signature_permit_receiver_field" value="{{ $receiverSign }}">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="date" name="permit_receiver_date" value="{{ $receiverDate }}" class="input w-full text-xs text-center">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="time" name="permit_receiver_time" value="{{ $receiverTime }}" class="input w-full text-xs text-center">
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
                </td>

                <!-- Issuer -->
                <td class="border text-center px-2 py-2">
                    <input type="text" name="close_issuer_name" class="input w-full text-xs mb-1" placeholder="Nama" value="{{ $closeIssuerName }}">
                    @if ($closeIssuerSign && file_exists(public_path($closeIssuerSign)))
                        <img src="{{ asset($closeIssuerSign) }}" alt="Tanda Tangan" class="h-20 mx-auto">
                    @else
                        <button type="button" onclick="openSignPad('signature_close_issuer')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    @endif
                
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
@include('components.sign-pad')
            </x-app-layout>