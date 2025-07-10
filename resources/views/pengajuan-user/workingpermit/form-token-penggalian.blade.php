@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded relative mb-4 text-sm">
        {{ session('success') }}
    </div>
@endif

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-sm text-gray-800 dark:text-gray-200 leading-tight">
            WORKING PERMIT PENGGALIAN
        </h2>
    </x-slot>

    <section class="bg-cover bg-center bg-no-repeat py-10 px-4" style="background-image: url('/images/bg-login.jpg');">
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-6">

 <form method="POST" action="{{ route('working-permit.penggalian.token.store', $permit->token) }}" enctype="multipart/form-data">
    @csrf
<input type="hidden" name="notification_id" value="{{ $notification->id ?? '' }}">
<!-- Izin Kerja Penggalian - Bagian 1: Detail Pekerjaan -->
<div class="text-center mb-4">
    <h2 class="text-2xl font-bold uppercase">IZIN KERJA PENGGALIAN</h2>
    <p class="text-sm mt-2 text-gray-600 leading-snug">
        Izin kerja ini diberikan untuk semua pekerjaan penggalian dengan kedalaman ≥ 300 mm, izin ini tidak berlaku untuk penggalian di area tambang aktif.
        Pekerjaan tidak bisa dimulai hingga izin kerja diverifikasi oleh <em>Permit Verificator</em>, diterbitkan oleh <em>Permit Issuer</em>, disahkan oleh 
        <em>Permit Authorizer</em>, dan <em>major hazards & control</em> disosialisasikan oleh <em>Permit Receiver</em>.
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
<!-- Bagian 2: Gambar/Denah Fasilitas Bawah Tanah -->
@php
    $checkedDenah = old('denah', json_decode($permit?->denah ?? '[]', true));
    $denahStatus = old('denah_status', $permit?->denah_status);
@endphp

<div x-data="{ showUpload: '{{ $denahStatus === 'ya' ? 'true' : 'false' }}' }" class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        2. Gambar/Denah Fasilitas Bawah Tanah yang Diperlukan 
        <span class="text-xs font-normal">(beri tanda centang)</span>
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3 text-sm">
        <div class="space-y-2">
            @foreach ([
                'kabel_listrik' => 'Jalur kabel listrik',
                'kabel_optik' => 'Jalur kabel optik/telpon',
                'pipa_gas' => 'Jalur pipa gas',
                'pipa_proses' => 'Jalur pipa proses',
                'cable_tunnel' => 'Jalur cable tunnel',
            ] as $val => $label)
                <label class="block">
                    <input type="checkbox" name="denah[]" value="{{ $val }}" class="mr-1"
                        {{ in_array($val, $checkedDenah) ? 'checked' : '' }}>
                    {{ $label }}
                </label>
            @endforeach
        </div>
        <div class="space-y-2">
            @foreach ([
                'pipa_air_hydrant' => 'Jalur pipa air <em>hydrant</em>',
                'pipa_air_utilitas' => 'Jalur pipa air utilitas',
                'kabel_instrumen' => 'Jalur kabel instrumentasi',
                'selokan_septic' => 'Jalur selokan dan <em>septic tank</em>',
                'lainnya' => 'Jalur lainnya ……………………',
            ] as $val => $label)
                <label class="block">
                    <input type="checkbox" name="denah[]" value="{{ $val }}" class="mr-1"
                        {{ in_array($val, $checkedDenah) ? 'checked' : '' }}>
                    {!! $label !!}
                </label>
            @endforeach
        </div>
    </div>

    <!-- Radio: Ya / N/A -->
    <p class="mt-4 text-sm">Jika ya, lampirkan untuk melengkapi izin penggalian ini:</p>
    <div class="flex gap-4 mt-2 text-sm">
        <label class="inline-flex items-center">
            <input type="radio" name="denah_status" value="ya" @click="showUpload = true"
                {{ $denahStatus === 'ya' ? 'checked' : '' }}>
            <span class="ml-2">Ya</span>
        </label>
        <label class="inline-flex items-center">
            <input type="radio" name="denah_status" value="na" @click="showUpload = false"
                {{ $denahStatus === 'na' ? 'checked' : '' }}>
            <span class="ml-2">N/A</span>
        </label>
    </div>

    <!-- Upload file -->
    <div x-show="showUpload" x-transition class="mt-4">
        <label class="block text-sm font-semibold mb-1">Upload Denah (PDF atau Gambar)</label>
        <input type="file" name="file_denah" accept=".pdf,.jpg,.jpeg,.png"
            class="block w-full text-sm text-gray-700 border border-gray-300 rounded p-2">

        @if (!empty($permit?->file_denah))
            <p class="text-xs text-blue-600 mt-1">
                <a href="{{ asset('storage/' . $permit->file_denah) }}" target="_blank" class="underline">
                    📎 Lihat file saat ini ({{ pathinfo($permit->file_denah, PATHINFO_EXTENSION) }})
                </a>
            </p>
        @endif

        <p class="text-xs text-gray-500 mt-1">Format diperbolehkan: PDF, JPG, PNG</p>
    </div>
</div>

<!-- Bagian 3: Persyaratan Kerja Aman -->
@php
    $syarat = old('syarat_penggalian', is_array($permit?->syarat_penggalian) ? $permit->syarat_penggalian : json_decode($permit?->syarat_penggalian ?? '[]', true));
    $items = [
        'Area penggalian sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
        'Area penggalian sudah dibatasi dengan memasang barikade atau <em>safety line</em>.',
        'Area kerja sudah diamankan dari potensi jatuhan benda/material.',
        'Area kerja sudah diamankan dari potensi pekerja terpeleset/tersandung.',
        'Pekerja yang melakukan pekerjaan penggalian kompeten terhadap pekerjaan yang dilakukan.',
        '<em>Job Safety Analysis/Safe Working Procedure</em> sudah tersedia dan semua pengendalian bahayanya sudah dilakukan.',
        'Peralatan kerja yang akan digunakan sudah diperiksa dan dipastikan layak untuk digunakan.',
        'Alat pelindung diri yang akan dipakai sudah diperiksa dan dinyatakan layak untuk dipakai.',
        'Semua pekerja penggalian sudah menggunakan alat pelindung diri yang sesuai.',
        'Tersedia jalur masuk/keluar galian yang aman, tersedia jembatan yang aman jika diperlukan.',
        'Tersedia tangga naik/turun dengan penempatan dan kemiringan yang aman (75°) dan terikat dengan kuat pada bibir galian.',
        'Semua pekerja penggalian memahami teknik <em>three point contact</em> saat naik/turun tangga.',
        'Area yang akan digali aman/sudah diamankan dari fasilitas bawah tanah seperti jalur kabel listrik, kabel optik, kabel instrumentasi, kabel telepon, pipa gas, pipa air <em>utility</em>, pipa proses, pipa air <em>hydrant</em>, selokan, <em>septic tank</em>, <em>cable tunnel</em>.',
        'Melakukan metoda <em>cutback</em> 30° untuk tanah keras dan 46° untuk tanah lunak/dipadatan.',
        'Metoda penyanggaan (<em>shoring</em>) lebih dari 3 meter disetujui oleh professional engineer.',
        'Penumpukan tanah galian minimum 2 meter dari bibir galian dan kemiringan kurang dari 45°.',
        'Garis barikade dengan jarak 2 meter dari bibir galian dan lengkap dengan rambu peringatan?',
        'Genangan air pada lubang galian dibuang/dipompa keluar dari lubang galian.',
        'Dipasang exhaust fan pada lubang galian untuk sirkulasi udara yang baik.',
        'Diterapkan <em>Protective support system</em> untuk penggalian yang akan dilakukan seperti <em>shoring/shielding/benching</em>.',
        'Dilakukan pengukuran kadar gas pada lubang galian (hasil pengukuran bisa dicatatkan pada lembar terpisah).',
        'Diperlukan isolasi dan penguncian sebelum penggalian dilakukan (ajukan Izin Kerja Umum).',
    ];
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 overflow-x-auto">
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
            @foreach ($items as $i => $item)
                <tr>
                    <td class="border px-2 py-1">{!! $item !!}</td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="syarat_penggalian[{{ $i }}]" value="ya"
                            {{ (isset($syarat[$i]) && $syarat[$i] === 'ya') ? 'checked' : '' }}>
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="syarat_penggalian[{{ $i }}]" value="na"
                            {{ (isset($syarat[$i]) && $syarat[$i] === 'na') ? 'checked' : '' }}>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Bagian 4: Rekomendasi Persyaratan Kerja Aman Tambahan -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        4. Rekomendasi Persyaratan Kerja Aman Tambahan dari 
        <em>Permit Verificator/Permit Issuer</em> 
        <span class="text-gray-300 text-xs">(jika ada)</span>
    </h3>

    <table class="table-auto w-full text-sm border mt-2">
        <tr>
            <td class="border px-2 py-2 align-top">
                <textarea name="rekomendasi_tambahan" class="w-full h-32 border rounded p-2 resize-none" placeholder="Tulis rekomendasi jika ada...">{{ old('rekomendasi_tambahan', $permit?->rekomendasi_tambahan) }}</textarea>
            </td>
            <td class="border text-center align-top px-4 py-2 w-24">
                <label class="block mb-1 font-medium">Ya</label>
                <input type="radio" name="rekomendasi_status" value="ya"
                    {{ old('rekomendasi_status', $permit?->rekomendasi_status) === 'ya' ? 'checked' : '' }}>
            </td>
        </tr>
    </table>
</div>

@php
    $reqName = old('permit_requestor_name', $permit?->permit_requestor_name ?? '');
    $reqSign = old('signature_permit_requestor', $permit?->signature_permit_requestor ?? '');
    $reqDateRaw = old('permit_requestor_date', $permit?->permit_requestor_date ?? '');
    $reqTimeRaw = old('permit_requestor_time', $permit?->permit_requestor_time ?? '');

    $reqDate = $reqDateRaw ? \Carbon\Carbon::parse($reqDateRaw)->format('Y-m-d') : '';
    $reqTime = $reqTimeRaw ? \Carbon\Carbon::parse($reqTimeRaw)->format('H:i') : '';
@endphp

<!-- Bagian 5: Permohonan Izin Kerja -->
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
                    <input type="text" name="permit_requestor_name" class="input w-full text-center"
                        value="{{ $reqName }}">
                </td>

                <td class="border px-2 py-2 text-center">
                    @if ($reqSign && file_exists(public_path($reqSign)))
                        <img src="{{ asset($reqSign) }}" alt="Tanda Tangan" class="h-16 mx-auto mb-1 border">
                    @endif

                    <button 
                        type="button"
                        onclick="openSignPad('penggalian_signature_permit_requestor')"
                        class="text-blue-600 underline text-xs mt-1">
                        {{ $reqSign ? 'Ubah Tanda Tangan' : 'Tanda Tangan' }}
                    </button>

                    <input 
                        type="hidden" 
                        name="signature_permit_requestor" 
                        id="penggalian_signature_permit_requestor" 
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
    $verifName = old('verificator_name', $permit?->verificator_name ?? '');
    $verifSign = old('signature_verificator', $permit?->signature_verificator ?? '');
    $verifDateRaw = old('verificator_date', $permit?->verificator_date ?? '');
    $verifTimeRaw = old('verificator_time', $permit?->verificator_time ?? '');

    $verifDate = $verifDateRaw ? \Carbon\Carbon::parse($verifDateRaw)->format('Y-m-d') : '';
    $verifTime = $verifTimeRaw ? \Carbon\Carbon::parse($verifTimeRaw)->format('H:i') : '';
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">6. Verifikasi Izin Kerja</h3>

    <div class="p-2 border-t-0 border border-gray-300">
        <p class="text-sm italic font-semibold mb-2">Digging Permit Verificator:</p>
        <p class="text-sm">
            Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman yang telah
            ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
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
                    <input type="text" name="verificator_name" class="input w-full text-center" value="{{ $verifName }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    @if ($verifSign && file_exists(public_path($verifSign)))
                        <img src="{{ asset($verifSign) }}" alt="Tanda Tangan" class="h-16 mx-auto mb-1 border">
                    @endif

                    <button 
                        type="button"
                        onclick="openSignPad('penggalian_signature_verificator')"
                        class="text-blue-600 underline text-xs mt-1">
                        {{ $verifSign ? 'Ubah Tanda Tangan' : 'Tanda Tangan' }}
                    </button>

                    <input 
                        type="hidden" 
                        name="signature_verificator" 
                        id="penggalian_signature_verificator" 
                        value="{{ $verifSign }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="verificator_date" class="input w-full text-center" value="{{ $verifDate }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="verificator_time" class="input w-full text-center" value="{{ $verifTime }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>

@php
    $issuerName = old('permit_issuer_name', $permit?->permit_issuer_name ?? '');
    $issuerSign = old('signature_permit_issuer', $permit?->signature_permit_issuer ?? '');
    $issuerDateRaw = old('permit_issuer_date', $permit?->permit_issuer_date ?? '');
    $issuerTimeRaw = old('permit_issuer_time', $permit?->permit_issuer_time ?? '');

    $startDate = old('izin_berlaku_dari', $permit?->izin_berlaku_dari ?? '');
    $startTime = old('izin_berlaku_jam_dari', $permit?->izin_berlaku_jam_dari ?? '');
    $endDate = old('izin_berlaku_sampai', $permit?->izin_berlaku_sampai ?? '');
    $endTime = old('izin_berlaku_jam_sampai', $permit?->izin_berlaku_jam_sampai ?? '');

    $issuerDate = $issuerDateRaw ? \Carbon\Carbon::parse($issuerDateRaw)->format('Y-m-d') : '';
    $issuerTime = $issuerTimeRaw ? \Carbon\Carbon::parse($issuerTimeRaw)->format('H:i') : '';
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">7. Penerbitan Izin Kerja</h3>

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
                    <input type="text" name="permit_issuer_name" class="input w-full text-center" value="{{ $issuerName }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    @if ($issuerSign && file_exists(public_path($issuerSign)))
                        <img src="{{ asset($issuerSign) }}" alt="Tanda Tangan" class="h-16 mx-auto mb-1 border">
                    @endif

                    <button 
                        type="button"
                        onclick="openSignPad('penggalian_signature_permit_issuer')"
                        class="text-blue-600 underline text-xs mt-1">
                        {{ $issuerSign ? 'Ubah Tanda Tangan' : 'Tanda Tangan' }}
                    </button>

                    <input 
                        type="hidden" 
                        name="signature_permit_issuer" 
                        id="penggalian_signature_permit_issuer" 
                        value="{{ $issuerSign }}">
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
            <label class="whitespace-nowrap font-medium">Dari Tanggal:</label>
            <input type="date" name="izin_berlaku_dari" class="input text-xs w-full" value="{{ $startDate }}">
            <label class="whitespace-nowrap font-medium">Jam:</label>
            <input type="time" name="izin_berlaku_jam_dari" class="input text-xs w-28" value="{{ $startTime }}">
        </div>
        <div class="flex items-center gap-2">
            <label class="whitespace-nowrap font-medium">Sampai Tanggal:</label>
            <input type="date" name="izin_berlaku_sampai" class="input text-xs w-full" value="{{ $endDate }}">
            <label class="whitespace-nowrap font-medium">Jam:</label>
            <input type="time" name="izin_berlaku_jam_sampai" class="input text-xs w-28" value="{{ $endTime }}">
        </div>
    </div>
</div>

@php
    $authName = old('permit_authorizer_name', $permit?->permit_authorizer_name ?? '');
    $authSign = old('signature_permit_authorizer', $permit?->signature_permit_authorizer ?? '');
    $authDateRaw = old('permit_authorizer_date', $permit?->permit_authorizer_date ?? '');
    $authTimeRaw = old('permit_authorizer_time', $permit?->permit_authorizer_time ?? '');

    $authDate = $authDateRaw ? \Carbon\Carbon::parse($authDateRaw)->format('Y-m-d') : '';
    $authTime = $authTimeRaw ? \Carbon\Carbon::parse($authTimeRaw)->format('H:i') : '';
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">8. Pengesahan Izin Kerja</h3>

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
                    <input type="text" name="permit_authorizer_name" class="input w-full text-center" value="{{ $authName }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    @if ($authSign && file_exists(public_path($authSign)))
                        <img src="{{ asset($authSign) }}" alt="Tanda Tangan" class="h-16 mx-auto mb-1 border">
                    @endif

                    <button 
                        type="button"
                        onclick="openSignPad('penggalian_signature_permit_authorizer')"
                        class="text-blue-600 underline text-xs mt-1">
                        {{ $authSign ? 'Ubah Tanda Tangan' : 'Tanda Tangan' }}
                    </button>

                    <input 
                        type="hidden" 
                        name="signature_permit_authorizer" 
                        id="penggalian_signature_permit_authorizer" 
                        value="{{ $authSign }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="permit_authorizer_date" class="input w-full text-center" value="{{ $authDate }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_authorizer_time" class="input w-full text-center" value="{{ $authTime }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>

@php
    $recvName = old('permit_receiver_name', $permit?->permit_receiver_name ?? '');
    $recvSign = old('signature_permit_receiver', $permit?->signature_permit_receiver ?? '');
    $recvDateRaw = old('permit_receiver_date', $permit?->permit_receiver_date ?? '');
    $recvTimeRaw = old('permit_receiver_time', $permit?->permit_receiver_time ?? '');

    $recvDate = $recvDateRaw ? \Carbon\Carbon::parse($recvDateRaw)->format('Y-m-d') : '';
    $recvTime = $recvTimeRaw ? \Carbon\Carbon::parse($recvTimeRaw)->format('H:i') : '';
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6">
    <h3 class="font-bold bg-black text-white px-2 py-1">9. Pelaksanaan Pekerjaan</h3>

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
                    <input type="text" name="permit_receiver_name" class="input w-full text-center" value="{{ $recvName }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    @if ($recvSign && file_exists(public_path($recvSign)))
                        <img src="{{ asset($recvSign) }}" alt="Tanda Tangan" class="h-16 mx-auto mb-1 border">
                    @endif

                    <button 
                        type="button"
                        onclick="openSignPad('penggalian_signature_permit_receiver')"
                        class="text-blue-600 underline text-xs mt-1">
                        {{ $recvSign ? 'Ubah Tanda Tangan' : 'Tanda Tangan' }}
                    </button>

                    <input 
                        type="hidden" 
                        name="signature_permit_receiver" 
                        id="penggalian_signature_permit_receiver" 
                        value="{{ $recvSign }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="permit_receiver_date" class="input w-full text-center" value="{{ $recvDate }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_receiver_time" class="input w-full text-center" value="{{ $recvTime }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>


<!-- Bagian 10: Penutupan Izin Kerja -->
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

@include('components.sign-pad')
</x-app-layout>