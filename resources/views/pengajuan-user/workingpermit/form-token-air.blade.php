@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded relative mb-4 text-sm">
        {{ session('success') }}
    </div>
@endif

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-sm text-gray-800 dark:text-gray-200 leading-tight">
            WORKING PERMIT AIR
        </h2>
    </x-slot>

    <section class="bg-cover bg-center bg-no-repeat py-10 px-4" style="background-image: url('/images/bg-login.jpg');">
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-6">
<form method="POST" action="{{ route('working-permit.air.token.store', $permit->token) }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="notification_id" value="{{ $notification->id ?? '' }}">

     <!-- Bagian 1: Detail Pekerjaan -->
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
    $daftarPekerja = old('daftar_pekerja', json_decode($permit?->daftar_pekerja ?? '[]', true));
    $sketsa = old('sketsa_pekerjaan') ?? $permit?->sketsa_pekerjaan;
@endphp

<div 
    x-data="{ pekerja: {{ json_encode($daftarPekerja ?: [['nama' => '', 'paraf' => '']]) }} }" 
    class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">

    <h3 class="font-bold bg-black text-white px-2 py-1">
        2. Daftar Pekerja dan Sketsa Pekerjaan 
        <span class="italic text-xs font-normal">(bisa dalam lampiran terpisah)</span>
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
        <!-- Tabel Daftar Pekerja -->
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
                                <input 
                                    type="text" 
                                    :name="`daftar_pekerja[${index}][nama]`" 
                                    x-model="row.nama" 
                                    class="input w-full text-xs">
                            </td>
                            <td class="border px-1 py-1 text-center">
                                <button 
                                    type="button"
                                    class="text-blue-600 underline text-xs"
                                    @click="openSignPad(`air_pekerja_${index}_paraf`)">
                                    Tanda Tangan
                                </button>

                                <input 
                                    type="hidden" 
                                    :id="`air_pekerja_${index}_paraf`" 
                                    :name="`daftar_pekerja[${index}][paraf]`" 
                                    x-model="row.paraf">

                                <template x-if="row.paraf">
                                    <img 
                                        :src="row.paraf" 
                                        class="mx-auto mt-1 h-10 border rounded shadow" />
                                </template>
                            </td>
                            <td class="border px-1 py-1 text-center">
                                <button 
                                    type="button" 
                                    @click="pekerja.splice(index, 1)" 
                                    class="text-red-500 text-xs">Hapus</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <button 
                type="button" 
                @click="pekerja.push({ nama: '', paraf: '' })"
                class="mt-2 text-blue-600 text-xs">
                + Tambah Baris
            </button>
        </div>

        <!-- Sketsa Pekerjaan -->
        <div class="border border-gray-300 rounded-md p-4 bg-gray-50 flex flex-col justify-between">
            <label class="text-sm font-semibold mb-2">Upload Sketsa Pekerjaan (jika diperlukan):</label>
            <input type="file" name="sketsa_pekerjaan" class="input text-sm">

            @if (!empty($sketsa))
                <div class="mt-3">
                    <p class="text-xs text-gray-600 italic mb-1">Sketsa sebelumnya:</p>
                    <a href="{{ asset($sketsa) }}" target="_blank">
                        @if (Str::endsWith($sketsa, ['.jpg', '.jpeg', '.png']))
                            <img src="{{ asset($sketsa) }}" alt="Sketsa" class="w-full max-h-64 object-contain border">
                        @else
                            <p class="text-xs text-blue-600 underline">Lihat File Sketsa</p>
                        @endif
                    </a>
                </div>
            @endif

            <p class="text-xs italic text-gray-500 mt-2">* Lampirkan gambar sketsa bila diperlukan</p>
        </div>
    </div>
</div>


<!-- Bagian 3: Persyaratan Kerja Aman -->
@php
    $persyaratanPerairan = [
        'Gunakan life jacket',
        'Pasang rambu-rambu area kerja air',
        'Periksa kondisi alat bantu apung',
        'Pastikan jalur evakuasi jelas',
        'Cek kondisi cuaca sebelum bekerja',
        'Gunakan alat komunikasi yang sesuai',
        'Pastikan ada pengawas kerja',
        'Lakukan briefing keselamatan sebelum mulai',
    ];

    $checklist = old('perairan', json_decode($permit?->persyaratan_perairan ?? '[]', true)); 
@endphp

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
            @foreach ($persyaratanPerairan as $i => $item)
                <tr>
                    <td class="border px-2 py-1">• {{ $item }}</td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="perairan[{{ $i }}]" value="ya" {{ ($checklist[$i] ?? '') === 'ya' ? 'checked' : '' }}>
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="perairan[{{ $i }}]" value="na" {{ ($checklist[$i] ?? '') === 'na' ? 'checked' : '' }}>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Bagian 4: Rekomendasi Persyaratan -->
@php
    $rekomendasiTambahan = old('rekomendasi_tambahan', $permit?->rekomendasi_kerja_aman ?? '');
    $rekomendasiStatus = old('rekomendasi_status', $permit?->rekomendasi_kerja_aman_check ?? '');
@endphp

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
                <textarea name="rekomendasi_tambahan" rows="6" class="w-full border-none text-sm focus:ring-0 focus:outline-none" placeholder="Tulis rekomendasi tambahan jika ada...">{{ $rekomendasiTambahan }}</textarea>
            </td>
            <td class="border text-center align-top" style="width: 60px;">
                <label class="inline-flex items-center">
                    <input type="radio" name="rekomendasi_status" value="ya" class="mr-1" {{ $rekomendasiStatus === 'ya' ? 'checked' : '' }}> Ya
                </label>
            </td>
        </tr>
    </table>
</div>
{{-- ======================= Bagian 5 ======================= --}}
@php
    $reqName = old('permit_requestor_name', $permit?->permit_requestor_name ?? '');
    $reqSign = old('signature_permit_requestor', $permit?->signature_permit_requestor ?? '');
    $reqDateRaw = old('permit_requestor_date', $permit?->permit_requestor_date);
    $reqTimeRaw = old('permit_requestor_time', $permit?->permit_requestor_time);
    $reqDate = $reqDateRaw ? \Carbon\Carbon::parse($reqDateRaw)->format('Y-m-d') : '';
    $reqTime = $reqTimeRaw ? \Carbon\Carbon::parse($reqTimeRaw)->format('H:i') : '';
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">5. Permohonan Izin Kerja (Air)</h3>
    <div class="bg-gray-100 border border-t-0 border-gray-300 p-3">
        <p class="italic font-semibold mb-1">Permit Requestor:</p>
        <p class="text-sm">
            Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan/atau rekomendasi persyaratan kerja aman tambahan dari 
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
                    <input type="text" name="permit_requestor_name"
                        class="input w-full text-xs text-center"
                        value="{{ $reqName }}" placeholder="Nama">
                </td>
                <td class="border px-2 py-2 text-center">
                    @if ($reqSign && file_exists(public_path($reqSign)))
                        <img src="{{ asset($reqSign) }}" class="h-16 mx-auto mb-1">
                    @endif
                    <button type="button"
                        onclick="openSignPad('signature_permit_requestor_air')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_permit_requestor" id="signature_permit_requestor_air"
                        value="{{ $reqSign }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="permit_requestor_date"
                        class="input w-full text-xs text-center"
                        value="{{ $reqDate }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_requestor_time"
                        class="input w-full text-xs text-center"
                        value="{{ $reqTime }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>
{{-- ======================= Bagian 6 ======================= --}}
@php
    $verifiedWorkers = old('verified_workers', json_decode($permit?->verified_workers ?? '[]', true));
    $verificatorName = old('verificator_name', $permit?->verificator_name ?? '');
    $verificatorSign = old('signature_verificator', $permit?->signature_verificator ?? '');
    $verificatorDateRaw = old('verificator_date', $permit?->verificator_date);
    $verificatorTimeRaw = old('verificator_time', $permit?->verificator_time);
    $verificatorDate = $verificatorDateRaw ? \Carbon\Carbon::parse($verificatorDateRaw)->format('Y-m-d') : '';
    $verificatorTime = $verificatorTimeRaw ? \Carbon\Carbon::parse($verificatorTimeRaw)->format('H:i') : '';
@endphp

<div x-data="{ workers: @js(!empty($verifiedWorkers) ? $verifiedWorkers : ['']) }"
    class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-xs">
    <h3 class="font-bold bg-black text-white px-2 py-1">6. Verifikasi Izin Kerja</h3>

    <div class="bg-gray-100 border border-t-0 border-gray-300 p-3">
        <p class="italic font-semibold mb-1">Working at Water Permit Verificator:</p>
        <p class="text-sm">
            Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman yang telah ditentukan dan/atau rekomendasi tambahan telah dipenuhi.
            <strong>Berikut nama-nama pekerja yang diizinkan untuk bekerja di air:</strong>
        </p>
    </div>

    <table class="table-auto w-full border text-xs mt-2">
        <tbody>
            <template x-for="(worker, index) in workers" :key="index">
                <tr>
                    <td class="border px-2 py-1 w-full">
                        <input type="text" :name="'verified_workers[' + index + ']'" x-model="workers[index]"
                            class="input w-full text-xs" placeholder="Nama Pekerja">
                    </td>
                    <td class="border px-2 py-1">
                        <button type="button" @click="workers.splice(index, 1)"
                            class="text-red-600 text-xs">Hapus</button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>

    <div class="mt-2">
        <button type="button" @click="workers.push('')"
            class="bg-blue-500 text-white px-2 py-1 rounded text-xs">+ Tambah Pekerja</button>
    </div>

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
                    <input type="text" name="verificator_name" class="input w-full text-xs text-center"
                        value="{{ $verificatorName }}">
                </td>
                <td class="border px-2 py-2">
                    @if ($verificatorSign && file_exists(public_path($verificatorSign)))
                        <img src="{{ asset($verificatorSign) }}" class="h-16 mx-auto mb-1">
                    @endif
                    <button type="button"
                        onclick="openSignPad('signature_verificator_air')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" id="signature_verificator_air" name="signature_verificator"
                        value="{{ $verificatorSign }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="date" name="verificator_date" class="input w-full text-xs text-center"
                        value="{{ $verificatorDate }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="time" name="verificator_time" class="input w-full text-xs text-center"
                        value="{{ $verificatorTime }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>
<!-- Bagian 7: Penerbitan Izin Kerja -->
@php
    $issuerName = old('permit_issuer_name', $permit?->permit_issuer_name);
    $issuerSign = old('signature_permit_issuer', $permit?->signature_permit_issuer);

    $seniorName = old('senior_manager_name', $permit?->senior_manager_name);
    $seniorSign = old('senior_manager_signature', $permit?->senior_manager_signature);

    $gmName = old('general_manager_name', $permit?->general_manager_name);
    $gmSign = old('general_manager_signature', $permit?->general_manager_signature);
@endphp
<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-xs">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        7. Penerbitan Izin Kerja 
        <span class="text-xs font-normal italic">(Tanda tangan General Manager jika diperlukan)</span>
    </h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="italic font-semibold mb-2">Permit Issuer & Senior Manager:</p>
        <p>Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman sudah dipenuhi untuk pekerjaan ini dapat dilakukan.</p>
    </div>

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
                {{-- Permit Issuer --}}
                <td class="border px-2 py-2">
                    <input type="text" name="permit_issuer_name" class="input w-full text-center mb-1 text-xs"
                        value="{{ old('permit_issuer_name', $permit?->permit_issuer_name) }}" placeholder="Nama">

                    @if ($permit?->signature_permit_issuer)
                        <img src="{{ asset($permit->signature_permit_issuer) }}" class="h-16 mx-auto mb-1">
                    @endif

                    <button type="button" onclick="openSignPad('air_signature_permit_issuer')" class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>

                    <input type="hidden" id="air_signature_permit_issuer" name="signature_permit_issuer"
                        value="{{ old('signature_permit_issuer', $permit?->signature_permit_issuer) }}">
                </td>

                {{-- Senior Manager --}}
                <td class="border px-2 py-2">
                    <input type="text" name="senior_manager_name" class="input w-full text-center mb-1 text-xs"
                        value="{{ old('senior_manager_name', $permit?->senior_manager_name) }}" placeholder="Nama">

                    @if ($permit?->senior_manager_signature)
                        <img src="{{ asset($permit->senior_manager_signature) }}" class="h-16 mx-auto mb-1">
                    @endif

                    <button type="button" onclick="openSignPad('air_senior_manager_signature')" class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>

                    <input type="hidden" id="air_senior_manager_signature" name="senior_manager_signature"
                        value="{{ old('senior_manager_signature', $permit?->senior_manager_signature) }}">
                </td>

                {{-- General Manager --}}
                <td class="border px-2 py-2">
                    <input type="text" name="general_manager_name" class="input w-full text-center mb-1 text-xs"
                        value="{{ old('general_manager_name', $permit?->general_manager_name) }}" placeholder="Nama">

                    @if ($permit?->general_manager_signature)
                        <img src="{{ asset($permit->general_manager_signature) }}" class="h-16 mx-auto mb-1">
                    @endif

                    <button type="button" onclick="openSignPad('air_general_manager_signature')" class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>

                    <input type="hidden" id="air_general_manager_signature" name="general_manager_signature"
                        value="{{ old('general_manager_signature', $permit?->general_manager_signature) }}">
                </td>
            </tr>
        </tbody>
    </table>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-4 text-xs">
        @foreach ([
            'izin_berlaku_dari' => 'Dari tanggal',
            'izin_berlaku_jam_dari' => 'Jam mulai',
            'izin_berlaku_sampai' => 'Sampai tanggal',
            'izin_berlaku_jam_sampai' => 'Jam selesai'
        ] as $field => $label)
            <div>
                <label class="block font-semibold mb-1">{{ $label }}:</label>
                <input type="{{ str_contains($field, 'jam') ? 'time' : 'date' }}"
                    name="{{ $field }}"
                    class="input w-full text-xs"
                    value="{{ old($field, $permit?->$field) }}">
            </div>
        @endforeach
    </div>
</div>

<!-- Bagian 8: Pengesahan Izin Kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">8. Pengesahan Izin Kerja</h3>
    <div class="bg-gray-100 border border-t-0 border-gray-300 p-3">
        <p class="italic font-semibold mb-1">Permit Authorizer:</p>
        <p class="text-sm">Saya menyatakan bahwa semua persyaratan sudah diperiksa dan pekerja memahami <em>major hazards</em> & pengendaliannya.</p>
    </div>

    <table class="table-auto w-full border text-sm mt-3 text-center">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1">Nama</th>
                <th class="border px-2 py-1">Tanda tangan</th>
                <th class="border px-2 py-1">Tanggal</th>
                <th class="border px-2 py-1">Jam</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-2">
                    <input type="text" name="permit_authorizer_name" class="input w-full text-xs text-center"
                        value="{{ old('permit_authorizer_name', $permit?->permit_authorizer_name) }}" placeholder="Nama">
                </td>
                <td class="border px-2 py-2">
                    @if ($permit?->signature_permit_authorizer)
                        <img src="{{ asset($permit->signature_permit_authorizer) }}" class="h-16 mx-auto mb-1">
                    @endif
                    <button type="button"
                        onclick="openSignPad('air_signature_permit_authorizer')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" id="air_signature_permit_authorizer" name="signature_permit_authorizer"
                        value="{{ old('signature_permit_authorizer', $permit?->signature_permit_authorizer) }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="date" name="permit_authorizer_date" class="input w-full text-xs text-center"
                        value="{{ old('permit_authorizer_date', $permit?->permit_authorizer_date) }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="time" name="permit_authorizer_time" class="input w-full text-xs text-center"
                        value="{{ old('permit_authorizer_time', $permit?->permit_authorizer_time) }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>
<!-- Bagian 9: Pelaksanaan Pekerjaan -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">9. Pelaksanaan Pekerjaan</h3>
    <div class="bg-gray-100 border border-t-0 border-gray-300 p-3">
        <p class="italic font-semibold mb-1">Permit Receiver:</p>
        <p class="text-sm">Saya sudah mensosialisasikan <em>major hazards</em> & pengendalian pekerjaan kepada seluruh pekerja terkait.</p>
    </div>

    <table class="table-auto w-full border text-sm mt-3 text-center">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1">Nama</th>
                <th class="border px-2 py-1">Tanda tangan</th>
                <th class="border px-2 py-1">Tanggal</th>
                <th class="border px-2 py-1">Jam</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-2">
                    <input type="text" name="permit_receiver_name" class="input w-full text-xs text-center"
                        value="{{ old('permit_receiver_name', $permit?->permit_receiver_name) }}" placeholder="Nama">
                </td>
                <td class="border px-2 py-2">
                    @if ($permit?->signature_permit_receiver)
                        <img src="{{ asset($permit->signature_permit_receiver) }}" class="h-16 mx-auto mb-1">
                    @endif
                    <button type="button"
                        onclick="openSignPad('air_signature_permit_receiver')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" id="air_signature_permit_receiver" name="signature_permit_receiver"
                        value="{{ old('signature_permit_receiver', $permit?->signature_permit_receiver) }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="date" name="permit_receiver_date" class="input w-full text-xs text-center"
                        value="{{ old('permit_receiver_date', $permit?->permit_receiver_date) }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="time" name="permit_receiver_time" class="input w-full text-xs text-center"
                        value="{{ old('permit_receiver_time', $permit?->permit_receiver_time) }}">
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