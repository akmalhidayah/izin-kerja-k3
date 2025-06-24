@php use Illuminate\Support\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-sm text-gray-800 dark:text-gray-200 leading-tight">
            WORKING PERMIT UMUM
        </h2>
    </x-slot>

    <section class="bg-cover bg-center bg-no-repeat py-10 px-4" style="background-image: url('/images/bg-login.jpg');">
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-6">

<form method="POST" action="{{ route('working-permit.umum.token.store', $permit->token) }}">
    @csrf
<div class="space-y-6">
    <h2 class="text-xl font-bold text-center">IZIN KERJA UMUM</h2>

   <!-- Bagian 1: Detail Pekerjaan -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow">
    <h3 class="font-bold bg-black text-white px-2 py-1">1. Detail Pekerjaan</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
        @if(isset($notification))
            <input type="hidden" name="notification_id" value="{{ $notification->id }}">
        @else
            <div class="text-red-600 text-sm font-semibold">❗ Notifikasi belum dibuat. Silakan isi notifikasi terlebih dahulu.</div>
        @endif

        <input type="text" name="lokasi_pekerjaan" placeholder="Lokasi Pekerjaan" class="input w-full"
               value="{{ old('lokasi_pekerjaan', $detail?->location) }}">
        <input type="date" name="tanggal_pekerjaan" placeholder="Tanggal" class="input w-full"
             value="{{ old('tanggal_pekerjaan', \Illuminate\Support\Carbon::parse($detail?->work_date)->format('Y-m-d')) }}">
    </div>

    <textarea name="uraian_pekerjaan" placeholder="Uraian pekerjaan" class="textarea w-full mt-2">{{ old('uraian_pekerjaan', $detail?->job_description) }}</textarea>
    <textarea name="peralatan_digunakan" placeholder="Peralatan/perlengkapan yang digunakan" class="textarea w-full mt-2">{{ old('peralatan_digunakan', $detail?->equipment) }}</textarea>
    <input type="number" name="jumlah_pekerja" placeholder="Perkiraan jumlah pekerja yang terlibat" class="input w-full mt-2"
           value="{{ old('jumlah_pekerja', $detail?->worker_count) }}">
    <input type="text" name="nomor_darurat" placeholder="Nomor darurat yang dapat dihubungi" class="input w-full mt-2"
           value="{{ old('nomor_darurat', $detail?->emergency_contact) }}">

    <p class="mt-3 font-semibold">Apakah diperlukan Izin Kerja Khusus?</p>
@php
    $izinKhususRaw = old('izin_khusus', $permit?->izin_khusus ?? []);
    $izinKhusus = is_array($izinKhususRaw) ? $izinKhususRaw : json_decode($izinKhususRaw, true) ?? [];
@endphp

    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-1 text-sm">
        @foreach ([
            'panas' => 'Pekerjaan panas berisiko tinggi',
            'ruang_tertutup' => 'Bekerja di ruang tertutup terbatas',
            'ketinggian' => 'Bekerja di ketinggian ≥ 1.8 meter',
            'penggalian' => 'Penggalian ≥ 30 cm',
            'perancah' => 'Perancah',
            'air' => 'Bekerja di air',
            'beban' => 'Mengangkat beban',
            'gas_panas' => 'Material/gas panas ≥ 150°C'
        ] as $value => $label)
            <label>
                <input type="checkbox" name="izin_khusus[]" value="{{ $value }}" class="mr-1"
                       {{ in_array($value, $izinKhusus) ? 'checked' : '' }}> {{ $label }}
            </label>
        @endforeach
    </div>
</div>
<!-- Bagian 2: Titik Isolasi dan Penguncian -->
@php
    $rawListrik = old('isolasi_listrik') ?? $permit?->isolasi_listrik;
    $rawNonListrik = old('isolasi_non_listrik') ?? $permit?->isolasi_non_listrik;

    $listrik = is_array($rawListrik) ? $rawListrik : json_decode($rawListrik, true);
    $nonListrik = is_array($rawNonListrik) ? $rawNonListrik : json_decode($rawNonListrik, true);
@endphp

<div 
    x-data="isolasiData(
        @js($listrik ?? []),
        @js($nonListrik ?? [])
    )"
>

    <h3 class="font-bold bg-black text-white px-2 py-1">2. Titik Isolasi dan Penguncian</h3>
    <p class="text-sm italic text-gray-600">Diisi oleh Isolation Officer</p>

    <!-- Isolasi Energi Listrik -->
    <div>
        <h4 class="mt-3 font-semibold">Isolasi Energi Listrik</h4>
        <table class="table-auto w-full text-sm border mt-1">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">Peralatan / Mesin</th>
                    <th class="p-2 border">Nomor</th>
                    <th class="p-2 border">Tempat Isolasi</th>
                    <th class="p-2 border">Locked / Tagged</th>
                    <th class="p-2 border">Tested</th>
                    <th class="p-2 border">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(item, index) in listrik" :key="index">
                    <tr>
                        <td class="border p-1">
                            <input type="text" class="input w-full" x-model="item.peralatan" :name="`isolasi_listrik[${index}][peralatan]`">
                        </td>
                        <td class="border p-1">
                            <input type="text" class="input w-full" x-model="item.nomor" :name="`isolasi_listrik[${index}][nomor]`">
                        </td>
                        <td class="border p-1">
                            <input type="text" class="input w-full" x-model="item.tempat" :name="`isolasi_listrik[${index}][tempat]`">
                        </td>
                        <td class="border p-1">
                            <input type="text" class="input w-full" x-model="item.locked" :name="`isolasi_listrik[${index}][locked]`">
                        </td>
                        <td class="border p-1">
                            <input type="text" class="input w-full" x-model="item.tested" :name="`isolasi_listrik[${index}][tested]`">
                        </td>
                        <td class="border p-1 text-center">
                            <input type="hidden" :id="`signature_listrik_${index}`" :name="`isolasi_listrik[${index}][signature]`" x-model="item.signature">
                            <template x-if="item.signature">
                                <img :src="item.signature" class="h-12 mx-auto mb-1">
                            </template>
                            <button type="button" @click="openSignPad(`signature_listrik_${index}`)" class="text-blue-600 underline text-xs">
                                Tanda Tangan
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <button @click="listrik.push({})" type="button" class="mt-2 text-xs bg-blue-500 text-white px-3 py-1 rounded">+ Tambah Baris</button>
    </div>

    <!-- Isolasi Energi Non Listrik -->
    <div>
        <h4 class="mt-3 font-semibold">Isolasi Energi Non Listrik</h4>
        <table class="table-auto w-full text-sm border mt-1">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">Peralatan / Sumber Energi</th>
                    <th class="p-2 border">Jenis</th>
                    <th class="p-2 border">Tempat Isolasi</th>
                    <th class="p-2 border">Locked / Tagged</th>
                    <th class="p-2 border">Tested</th>
                    <th class="p-2 border">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(item, index) in nonListrik" :key="index">
                    <tr>
                        <td class="border p-1">
                            <input type="text" class="input w-full" x-model="item.peralatan" :name="`isolasi_non_listrik[${index}][peralatan]`">
                        </td>
                        <td class="border p-1">
                            <input type="text" class="input w-full" x-model="item.jenis" :name="`isolasi_non_listrik[${index}][jenis]`">
                        </td>
                        <td class="border p-1">
                            <input type="text" class="input w-full" x-model="item.tempat" :name="`isolasi_non_listrik[${index}][tempat]`">
                        </td>
                        <td class="border p-1">
                            <input type="text" class="input w-full" x-model="item.locked" :name="`isolasi_non_listrik[${index}][locked]`">
                        </td>
                        <td class="border p-1">
                            <input type="text" class="input w-full" x-model="item.tested" :name="`isolasi_non_listrik[${index}][tested]`">
                        </td>
                        <td class="border p-1 text-center">
                            <input type="hidden" :id="`signature_nonlistrik_${index}`" :name="`isolasi_non_listrik[${index}][signature]`" x-model="item.signature">
                            <template x-if="item.signature">
                                <img :src="item.signature" class="h-12 mx-auto mb-1">
                            </template>
                            <button type="button" @click="openSignPad(`signature_nonlistrik_${index}`)" class="text-blue-600 underline text-xs">
                                Tanda Tangan
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <button @click="nonListrik.push({})" type="button" class="mt-2 text-xs bg-blue-500 text-white px-3 py-1 rounded">+ Tambah Baris</button>
    </div>
</div>

<!-- Bagian 3: Persyaratan Kerja Aman -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto">
    <h3 class="font-bold bg-black text-white px-2 py-1">3. Persyaratan Kerja Aman</h3>

    @php
        $items = [
            'Area kerja sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
            'Area kerja sudah dibatasi dengan memasang barikade atau <em>safety line</em>.',
            'Area kerja sudah diamankan dari potensi jatuhan benda/material.',
            'Area kerja sudah diamankan dari potensi pekerja terpeleset/tersandung.',
            'Pekerja yang melakukan pekerjaan kompeten terhadap pekerjaan yang dilakukan.',
            '<em>Job Safety Analysis</em>/<em>Safe Working Procedure</em> sudah tersedia dan semua pengendalian bahayanya sudah dilakukan.',
            'Peralatan/perlengkapan kerja yang akan digunakan sudah diperiksa dan dipastikan layak dan aman untuk digunakan.',
            'Alat pelindung diri yang akan dipakai sudah diperiksa dan dinyatakan layak dan aman untuk dipakai.',
            'Semua pekerja yang terlibat sudah menggunakan alat pelindung diri yang sesuai.',
            'Bahan/material yang akan dipakai/dikerjakan sudah diperiksa dan dinyatakan aman.',
            'Bahan mudah terbakar sudah dipindahkan/disingkirkan dari area kerja (>10 meter).',
            'APAR/sarana pemadam api lainnya sudah disediakan untuk pekerjaan panas di area kerja.',
            'Selimut tahan api, perisai panas sudah disediakan dan area di bawah pekerjaan panas sudah dibatasi dengan barikade/<em>safety line</em>.',
            '<em>Ada Fire Sentry</em> untuk pekerjaan panas yang akan dilakukan.',
            'Semua peralatan dan permesinan sudah diisolasi, dikunci, diuji dan ditandai oleh <em>Isolation Officer</em>.',
            'Semua pekerja sudah memasang <em>personal lock</em> pada titik isolasi yang sudah ditentukan.',
        ];

        // Ambil nilai checklist dari old() atau fallback ke database
        $checklist = old('checklist_kerja_aman') ?: json_decode($permit?->checklist_kerja_aman ?? '{}', true);
    @endphp

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-left">Keterangan</th>
                <th class="border px-2 py-1 text-center">Pemohon Kerja (O)</th>
                <th class="border px-2 py-1 text-center">Penerbit Kerja (V)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $i => $item)
                <tr>
                    <td class="border px-2 py-1">{!! $item !!}</td>
                    <td class="border px-2 py-1 text-center space-x-3">
                        <label>
                            <input type="radio" name="checklist_kerja_aman[pemohon][{{ $i }}]" value="ya"
                                {{ ($checklist['pemohon'][$i] ?? '') === 'ya' ? 'checked' : '' }}>
                            Ya
                        </label>
                        <label class="ml-2">
                            <input type="radio" name="checklist_kerja_aman[pemohon][{{ $i }}]" value="na"
                                {{ ($checklist['pemohon'][$i] ?? '') === 'na' ? 'checked' : '' }}>
                            N/A
                        </label>
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <input type="checkbox" name="checklist_kerja_aman[penerbit][{{ $i }}]" value="1"
                            {{ !empty($checklist['penerbit'][$i]) ? 'checked' : '' }}>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<!-- Bagian 4: Rekomendasi Persyaratan Kerja Aman Tambahan -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto">
    @php
        $rekomendasiTambahan = old('rekomendasi_tambahan', $permit?->rekomendasi_tambahan ?? '');
        $rekomendasiStatus = old('rekomendasi_status', $permit?->rekomendasi_status ?? '');
    @endphp

    <table class="table-auto w-full text-sm border">
        <thead>
            <tr class="bg-black text-white">
                <th colspan="2" class="text-left px-2 py-1">
                    4. Rekomendasi Persyaratan Kerja Aman Tambahan dari <em>Permit Issuer</em> <span class="text-gray-300 text-xs">(jika ada)</span>
                </th>
                <th class="text-center px-2 py-1 w-20">(O)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2" class="border px-2 py-2">
                    <textarea name="rekomendasi_tambahan" class="w-full h-24 border rounded p-2 resize-none" placeholder="Catatan dari Permit Issuer (jika ada)">{{ $rekomendasiTambahan }}</textarea>
                </td>
                <td class="border text-center align-top pt-3">
                    <label class="block">
                        <input type="radio" name="rekomendasi_status" value="ya" {{ $rekomendasiStatus == 'ya' ? 'checked' : '' }}> Ya
                    </label>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Bagian 5: Permohonan Izin Kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">5. Permohonan Izin Kerja</h3>
@php

    $requestorName = old('permit_requestor_name', $permit?->permit_requestor_name ?? '');
    $requestorSign = old('signature_permit_requestor', $permit?->permit_requestor_sign ?? '');

    $requestorDateRaw = old('permit_requestor_date', $permit?->permit_requestor_date ?? '');
    $requestorDate = $requestorDateRaw ? \Carbon\Carbon::parse($requestorDateRaw)->format('Y-m-d') : '';

    $requestorTimeRaw = old('permit_requestor_time', $permit?->permit_requestor_time ?? '');
    $requestorTime = $requestorTimeRaw ? \Carbon\Carbon::parse($requestorTimeRaw)->format('H:i') : '';
@endphp


    <div class="border border-t-0 border-gray-300 p-3">
        <p class="text-sm">
            <strong><em>Permit Requestor:</em></strong><br>
            Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari <em>Permit Issuer</em> telah dipenuhi untuk dapat melakukan pekerjaan ini.
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
                    <input type="text" name="permit_requestor_name" class="input w-full text-center" value="{{ $requestorName }}">
                </td>
              <td class="border px-2 py-2 text-center">
    @if ($requestorSign)
        <img src="{{ asset($requestorSign) }}" alt="Tanda Tangan" class="h-20 mx-auto">
    @else
        <button 
            type="button"
            onclick="openSignPad('signature_permit_requestor')"
            class="text-blue-600 underline text-xs">
            Tanda Tangan
        </button>
    @endif
    <input type="hidden" name="signature_permit_requestor" id="signature_permit_requestor" value="{{ $requestorSign }}">
</td>

                <td class="border px-2 py-2 text-center">
                    <input type="date" name="permit_requestor_date" class="input w-full text-center" value="{{ $requestorDate }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_requestor_time" class="input w-full text-center" value="{{ $requestorTime }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>
<!-- Bagian 6: Penerbitan Izin Kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">6. Penerbitan Izin Kerja</h3>
@php
    $issuerName = old('permit_issuer_name', $permit?->permit_issuer_name ?? '');
    $issuerSign = old('signature_permit_issuer', $permit?->permit_issuer_sign ?? '');

    $issuerDateRaw = old('permit_issuer_date', $permit?->permit_issuer_date ?? '');
    $issuerTimeRaw = old('permit_issuer_time', $permit?->permit_issuer_time ?? '');

    $izinDariDate = old('izin_berlaku_dari', $permit?->izin_berlaku_dari ?? '');
    $izinDariTime = old('izin_berlaku_jam_dari', $permit?->izin_berlaku_jam_dari ?? '');
    $izinSampaiDate = old('izin_berlaku_sampai', $permit?->izin_berlaku_sampai ?? '');
    $izinSampaiTime = old('izin_berlaku_jam_sampai', $permit?->izin_berlaku_jam_sampai ?? '');

    $issuerDate = $issuerDateRaw ? Carbon::parse($issuerDateRaw)->format('Y-m-d') : '';
    $issuerTime = $issuerTimeRaw ? Carbon::parse($issuerTimeRaw)->format('H:i') : '';
@endphp

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
                        <input type="text" name="permit_issuer_name" class="input w-full text-center" value="{{ $issuerName }}">
                    </td>
                  <td class="border px-2 py-2 text-center">
    @if ($issuerSign)
        <img src="{{ asset($issuerSign) }}" alt="Tanda Tangan" class="h-20 mx-auto">
    @else
        <button 
            type="button"
            onclick="openSignPad('signature_permit_issuer')"
            class="text-blue-600 underline text-xs">
            Tanda Tangan
        </button>
    @endif
    <input type="hidden" name="signature_permit_issuer" id="signature_permit_issuer" value="{{ $issuerSign }}">
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
    </div>

    <!-- Waktu izin berlaku -->
    <div class="mt-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
            <div class="flex items-center gap-2">
                <label class="whitespace-nowrap font-medium">Dari Tanggal:</label>
                <input type="date" name="izin_berlaku_dari" class="input text-xs w-full" value="{{ $izinDariDate }}">
                <label class="whitespace-nowrap font-medium">Jam:</label>
                <input type="time" name="izin_berlaku_jam_dari" class="input text-xs w-28" value="{{ $izinDariTime }}">
            </div>
            <div class="flex items-center gap-2">
                <label class="whitespace-nowrap font-medium">Sampai Tanggal:</label>
                <input type="date" name="izin_berlaku_sampai" class="input text-xs w-full" value="{{ $izinSampaiDate }}">
                <label class="whitespace-nowrap font-medium">Jam:</label>
                <input type="time" name="izin_berlaku_jam_sampai" class="input text-xs w-28" value="{{ $izinSampaiTime }}">
            </div>
        </div>
    </div>
</div>
@php
    $authorizerName = old('permit_authorizer_name', $permit?->permit_authorizer_name ?? '');
    $authorizerSign = old('signature_permit_authorizer', $permit?->permit_authorizer_sign ?? '');

    $authorizerDateRaw = old('permit_authorizer_date', $permit?->permit_authorizer_date ?? '');
    $authorizerDate = $authorizerDateRaw ? Carbon::parse($authorizerDateRaw)->format('Y-m-d') : '';

    $authorizerTimeRaw = old('permit_authorizer_time', $permit?->permit_authorizer_time ?? '');
    $authorizerTime = $authorizerTimeRaw ? Carbon::parse($authorizerTimeRaw)->format('H:i') : '';
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">7. Pengesahan Izin Kerja</h3>

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
                    <input type="text" name="permit_authorizer_name" class="input w-full text-center" value="{{ $authorizerName }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    @if ($authorizerSign)
                        <img src="{{ asset($authorizerSign) }}" alt="Tanda Tangan" class="h-20 mx-auto">
                    @else
                        <button 
                            type="button"
                            onclick="openSignPad('signature_permit_authorizer')"
                            class="text-blue-600 underline text-xs">
                            Tanda Tangan
                        </button>
                    @endif
                    <input type="hidden" name="signature_permit_authorizer" id="signature_permit_authorizer" value="{{ $authorizerSign }}">
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

    $receiverDateRaw = old('permit_receiver_date', $permit?->permit_receiver_date ?? '');
    $receiverDate = $receiverDateRaw ? \Carbon\Carbon::parse($receiverDateRaw)->format('Y-m-d') : '';

    $receiverTimeRaw = old('permit_receiver_time', $permit?->permit_receiver_time ?? '');
    $receiverTime = $receiverTimeRaw ? \Carbon\Carbon::parse($receiverTimeRaw)->format('H:i') : '';
@endphp

<!-- Bagian 8 : Pelaksanaan Pekerjaan -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">8. Pelaksanaan Pekerjaan</h3>

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
                    <input type="text" name="permit_receiver_name" class="input w-full text-center" value="{{ $receiverName }}">
                </td>
                <td class="border px-2 py-2 text-center">
               @if ($receiverSign)
    <img src="{{ asset($receiverSign) }}" alt="Tanda Tangan" class="h-20 mx-auto">
@else
    <button 
        type="button"
        onclick="openSignPad('signature_permit_receiver')"
        class="text-blue-600 underline text-xs">
        Tanda Tangan
    </button>
@endif

                    <input type="hidden" name="signature_permit_receiver" id="signature_permit_receiver" value="{{ $receiverSign }}">
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
$liveTestingSign = old('signature_live_testing', $permit?->live_testing_sign ?? '');

    $liveTestingName = old('live_testing_name', $permit?->live_testing_name ?? '');
    $liveTestingDateRaw = old('live_testing_date', $permit?->live_testing_date ?? '');
    $liveTestingDate = $liveTestingDateRaw ? \Carbon\Carbon::parse($liveTestingDateRaw)->format('Y-m-d') : '';
    $liveTestingTimeRaw = old('live_testing_time', $permit?->live_testing_time ?? '');
    $liveTestingTime = $liveTestingTimeRaw ? \Carbon\Carbon::parse($liveTestingTimeRaw)->format('H:i') : '';
    $liveTestingChecklist = old('live_testing', json_decode($permit?->live_testing_items ?? '[]', true));
@endphp

<!-- Bagian 9: Live Testing -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">9. <em>Live Testing</em> 
        <span class="text-xs font-normal">(Jika ada dan diisi oleh <em>Isolation Officer</em>)</span>
    </h3>

    <p class="text-sm italic mt-1 text-gray-700">
        Jika peralatan/permesinan harus dinyalakan kembali (<em>live testing</em>), hal-hal berikut harus dilengkapi untuk memastikan pekerjaan dan kondisi area aman. 
        Petugas Isolasi HIL harus memberi paraf untuk setiap tahap apabila sudah dilengkapi.
    </p>

    <table class="table-auto w-full text-sm border mt-3">
        <tbody>
            @php
                $liveTestingItems = [
                    "Peralatan/permesinan yang akan dinyalakan kembali (re-energize) sudah diidentifikasi.",
                    "Semua peralatan kerja sudah dipindahkan/diamankan dari area permesinan yang akan dinyalakan kembali.",
                    "Semua orang yang dekat dengan area kerja sudah diinformasikan adanya peralatan/permesinan yang akan dinyalakan.",
                    "Orang-orang yang tidak berkepentingan harus berada di luar area.",
                    "<em>Machine guarding</em> pada peralatan/permesinan yang tidak memerlukan proses <em>live testing</em> sudah dipasang kembali.",
                    "Semua <em>lock</em> dan <em>tag</em> sudah dilepaskan.",
                    "<em>Standby person</em> telah ditunjuk untuk memastikan bahwa tidak ada orang di sekitar area dimana terdapat peralatan mesin tanpa <em>machine guarding</em>.",
                    "Peralatan/permesinan sudah dinyalakan kembali.",
                    "<em>Setelah live testing, isolasi & penguncian harus kembali dipasang apabila pekerjaan belum selesai.</em>"
                ];
            @endphp

            @foreach ($liveTestingItems as $i => $item)
                <tr>
                    <td class="border px-2 py-1">{!! $item !!}</td>
                    <td class="border px-2 py-1 text-center w-12">
                        <input type="checkbox" name="live_testing[{{ $i }}]" value="ya"
                               {{ (!empty($liveTestingChecklist[$i]) || old("live_testing.$i")) ? 'checked' : '' }}>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Signature Section -->
    <table class="table-auto w-full text-sm border mt-4">
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
                    <input type="text" name="live_testing_name" class="input w-full text-center" value="{{ $liveTestingName }}">
                </td>
                <td class="border text-center px-2 py-2">
                 @if ($liveTestingSign)
    <img src="{{ asset($liveTestingSign) }}" alt="Tanda Tangan" class="h-20 mx-auto">
@else
    <button type="button" onclick="openSignPad('signature_live_testing')" class="text-blue-600 underline text-xs">
        Tanda Tangan
    </button>
@endif

                    <input type="hidden" name="signature_live_testing" id="signature_live_testing" value="{{ $liveTestingSign }}">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="date" name="live_testing_date" class="input w-full text-center" value="{{ $liveTestingDate }}">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="time" name="live_testing_time" class="input w-full text-center" value="{{ $liveTestingTime }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>
@php
    $closeDate = old('close_date', $closure?->closed_date);
    $closeTime = old('close_time', $closure?->closed_time);
    $closeRequestorName = old('close_requestor_name', $closure?->requestor_name);
    $closeIssuerName = old('close_issuer_name', $closure?->issuer_name);
    $closeRequestorSign = old('signature_close_requestor', $closure?->requestor_sign ?? '');
    $closeIssuerSign = old('signature_close_issuer', $closure?->issuer_sign ?? '');

    $closeLock = old('close_lock_tag', $closure?->lock_tag_removed ? 'ya' : 'na');
    $closeTools = old('close_tools', $closure?->equipment_cleaned ? 'ya' : 'na');
    $closeGuarding = old('close_guarding', $closure?->guarding_restored ? 'ya' : 'na');
@endphp

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
                    <label><input type="radio" name="close_lock_tag" value="ya" {{ $closeLock === 'ya' ? 'checked' : '' }}> Ya</label>
                    <label class="ml-2"><input type="radio" name="close_lock_tag" value="na" {{ $closeLock === 'na' ? 'checked' : '' }}> N/A</label>
                </td>
            </tr>
            <tr>
                <td class="border px-2 py-1 font-semibold">Sampah & Peralatan Kerja</td>
                <td class="border px-2 py-1">Semua sampah sudah dibersihkan dan peralatan kerja sudah diamankan</td>
                <td class="border px-2 py-1 text-center">
                    <label><input type="radio" name="close_tools" value="ya" {{ $closeTools === 'ya' ? 'checked' : '' }}> Ya</label>
                    <label class="ml-2"><input type="radio" name="close_tools" value="na" {{ $closeTools === 'na' ? 'checked' : '' }}> N/A</label>
                </td>
            </tr>
            <tr>
                <td class="border px-2 py-1 font-semibold">Machine Guarding</td>
                <td class="border px-2 py-1">Semua <em>machine guarding</em> sudah dipasang kembali</td>
                <td class="border px-2 py-1 text-center">
                    <label><input type="radio" name="close_guarding" value="ya" {{ $closeGuarding === 'ya' ? 'checked' : '' }}> Ya</label>
                    <label class="ml-2"><input type="radio" name="close_guarding" value="na" {{ $closeGuarding === 'na' ? 'checked' : '' }}> N/A</label>
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
                <td class="border text-center px-2 py-2">
                    <input type="date" name="close_date" class="input w-full text-center" value="{{ $closeDate }}">
                </td>
                <td class="border text-center px-2 py-2">
                    <input type="time" name="close_time" class="input w-full text-center" value="{{ $closeTime }}">
                </td>

                <!-- Permit Requestor -->
                <td class="border text-center px-2 py-2">
                    <input type="text" name="close_requestor_name" class="input w-full text-xs mb-1" placeholder="Nama" value="{{ $closeRequestorName }}">
                 @if ($closeRequestorSign && file_exists(public_path($closeRequestorSign)))
    <img src="{{ asset($closeRequestorSign) }}" alt="Tanda Tangan" class="h-20 mx-auto">
@else
    <button type="button" onclick="openSignPad('signature_close_requestor')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
@endif

                    <input type="hidden" name="signature_close_requestor" id="signature_close_requestor" value="{{ $closeRequestorSign }}">
                </td>

                <!-- Permit Issuer -->
                <td class="border text-center px-2 py-2">
                    <input type="text" name="close_issuer_name" class="input w-full text-xs mb-1" placeholder="Nama" value="{{ $closeIssuerName }}">
                    @if ($closeIssuerSign)
                        <img src="{{ asset($closeIssuerSign) }}" alt="Tanda Tangan" class="h-20 mx-auto">
                    @else
                        <button type="button" onclick="openSignPad('signature_close_issuer')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    @endif
                    <input type="hidden" name="signature_close_issuer" id="signature_close_issuer" value="{{ $closeIssuerSign }}">
                </td>
            </tr>
        </tbody>
    </table>
    <table class="table-auto w-full text-sm border mt-4">
    <tr>
        <td class="border px-2 py-1 font-semibold w-64">Jumlah RFID yang diberikan ke kontraktor</td>
        <td class="border px-2 py-1 text-left" colspan="3">
            <div class="flex items-center gap-2">
                <input type="number" name="jumlah_rfid" min="0" class="input w-28 text-center"
                    value="{{ old('jumlah_rfid', $closure?->jumlah_rfid) }}">
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
</div>
<script>
function isolasiData(existingListrik = [], existingNonListrik = []) {
    let listrik = Array.isArray(existingListrik) ? existingListrik : (existingListrik ? JSON.parse(existingListrik) : []);
    let nonListrik = Array.isArray(existingNonListrik) ? existingNonListrik : (existingNonListrik ? JSON.parse(existingNonListrik) : []);
    
    return {
        listrik: listrik.length ? listrik : [{ peralatan: '', nomor: '', tempat: '', locked: '', tested: '', signature: '' }],
        nonListrik: nonListrik.length ? nonListrik : [{ peralatan: '', jenis: '', tempat: '', locked: '', tested: '', signature: '' }],
        addListrik() { this.listrik.push({ peralatan: '', nomor: '', tempat: '', locked: '', tested: '', signature: '' }); },
        addNonListrik() { this.nonListrik.push({ peralatan: '', jenis: '', tempat: '', locked: '', tested: '', signature: '' }); },
    };
}
</script>

</x-app-layout>