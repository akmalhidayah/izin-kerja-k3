<form method="POST" action="{{ route('working-permit.air.store') }}" enctype="multipart/form-data">
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

    <!-- Bagian 2: Daftar Pekerja dan Sketsa Pekerjaan -->
   @php
    $daftarPekerja = old('daftar_pekerja', json_decode($permit?->daftar_pekerja ?? '[]', true));
@endphp

<div x-data="{ pekerja: {{ json_encode($daftarPekerja ?: [['nama' => '', 'paraf' => '']]) }} }" class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        2. Daftar Pekerja dan Sketsa Pekerjaan 
        <span class="italic text-xs font-normal">(bisa dalam lampiran terpisah)</span>
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
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
                                <input type="text" :name="'nama_pekerja[' + index + ']'" x-model="row.nama" class="input w-full text-xs">
                            </td>
                            <td class="border px-1 py-1">
                                <input type="text" :name="'paraf_pekerja[' + index + ']'" x-model="row.paraf" class="input w-full text-xs">
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
        <div class="border border-gray-300 rounded-md p-4 bg-gray-50 flex flex-col justify-between">
            <label class="text-sm font-semibold mb-2">Upload Sketsa Pekerjaan (jika diperlukan):</label>
            <input type="file" name="sketsa_pekerjaan" class="input text-sm">
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

<!-- bagian 5 permohonan izin kerja -->
@php
    $reqName = old('permit_requestor_name', $permit?->permit_requestor_name ?? '');
    $reqSign = old('permit_requestor_sign', $permit?->permit_requestor_sign ?? '');
    $reqDate = old('permit_requestor_date', $permit?->permit_requestor_date ?? '');
    $reqTime = old('permit_requestor_time', $permit?->permit_requestor_time ?? '');
@endphp

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
                    <input type="text" name="permit_requestor_name" class="input w-full text-xs text-center" value="{{ $reqName }}" placeholder="Nama">
                </td>
                <td class="border px-2 py-2 text-center">
                    @if ($reqSign)
                        <img src="{{ asset($reqSign) }}" alt="Tanda Tangan" class="h-16 mx-auto">
                    @else
                        <button 
                            type="button"
                            @click="Alpine.store('signatureModal').openModal('Permit Requestor')"
                            class="text-blue-600 underline text-xs">
                            Tanda Tangan
                        </button>
                    @endif
                    <input type="hidden" name="permit_requestor_sign" value="{{ $reqSign }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="date" name="permit_requestor_date" class="input w-full text-xs text-center" value="{{ $reqDate }}">
                </td>
                <td class="border px-2 py-2 text-center">
                    <input type="time" name="permit_requestor_time" class="input w-full text-xs text-center" value="{{ $reqTime }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- bagian 6 verifikasi izin kerja -->
 @php
    $verifiedWorkers = old('verified_workers', json_decode($permit?->verified_workers ?? '[]', true));
    $verificatorName = old('verificator_name', $permit?->verificator_name ?? '');
    $verificatorSign = old('verificator_sign', $permit?->verificator_sign ?? '');
    $verificatorDate = old('verificator_date', $permit?->verificator_date ?? '');
    $verificatorTime = old('verificator_time', $permit?->verificator_time ?? '');
@endphp

<div x-data="{ workers: @js($verifiedWorkers ?: ['']) }" class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-xs">
    <h3 class="font-bold bg-black text-white px-2 py-1">6. Verifikasi Izin Kerja</h3>

    <div class="bg-gray-100 border border-t-0 border-gray-300 p-3">
        <p class="italic font-semibold mb-1">Working at Water Permit Verificator:</p>
        <p class="text-sm">
            Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
            <strong> Berikut nama-nama pekerja yang diizinkan untuk bekerja di air:</strong>
        </p>
    </div>

    <table class="table-auto w-full border text-xs mt-2">
        <tbody>
            <template x-for="(worker, index) in workers" :key="index">
                <tr>
                    <td class="border px-2 py-1 w-full">
                        <input type="text" :name="'verified_workers[' + index + ']'" x-model="workers[index]" class="input w-full text-xs" placeholder="Nama Pekerja">
                    </td>
                    <td class="border px-2 py-1">
                        <button type="button" @click="workers.splice(index, 1)" class="text-red-600 text-xs">Hapus</button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>

    <div class="mt-2">
        <button type="button" @click="workers.push('')" class="bg-blue-500 text-white px-2 py-1 rounded text-xs">+ Tambah Pekerja</button>
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
                    <input type="text" name="verificator_name" class="input w-full text-xs text-center" value="{{ $verificatorName }}">
                </td>
                <td class="border px-2 py-2">
                    @if ($verificatorSign)
                        <img src="{{ asset($verificatorSign) }}" class="h-16 mx-auto">
                    @else
                        <button type="button" @click="Alpine.store('signatureModal').openModal('Working at Water Permit Verificator')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    @endif
                    <input type="hidden" name="verificator_sign" value="{{ $verificatorSign }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="date" name="verificator_date" class="input w-full text-xs text-center" value="{{ $verificatorDate }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="time" name="verificator_time" class="input w-full text-xs text-center" value="{{ $verificatorTime }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- bagian 7 penerbitan izin kerja -->
@php
    $issuerName = old('permit_issuer_name', $permit?->permit_issuer_name);
    $issuerSign = old('permit_issuer_signature', $permit?->signature_permit_issuer);

    $seniorName = old('senior_manager_name', $permit?->senior_manager_name);
    $seniorSign = old('senior_manager_signature', $permit?->senior_manager_signature);

    $gmName = old('general_manager_name', $permit?->general_manager_name);
    $gmSign = old('general_manager_signature', $permit?->general_manager_signature);
@endphp

<!-- Bagian 7: Penerbitan Izin Kerja -->
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
                @foreach (['permit_issuer' => 'Permit Issuer', 'senior_manager' => 'Senior Manager', 'general_manager' => 'General Manager'] as $field => $label)
                @php
                    $nameField = $field.'_name';
                    $signField = 'signature_'.$field;
                @endphp
                <td class="border px-2 py-2">
                    <input type="text" name="{{ $nameField }}" class="input w-full text-center mb-1 text-xs" value="{{ old($nameField, $permit?->$nameField) }}" placeholder="Nama">
                    @if ($permit?->$signField)
                        <img src="{{ asset($permit->$signField) }}" class="h-16 mx-auto">
                    @else
                        <button type="button" @click="Alpine.store('signatureModal').openModal('{{ $label }}')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    @endif
                    <input type="hidden" name="{{ $signField }}" value="{{ old($signField, $permit?->$signField) }}">
                </td>
                @endforeach
            </tr>
        </tbody>
    </table>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-4 text-xs">
        @foreach (['izin_berlaku_dari' => 'Dari tanggal', 'izin_berlaku_jam_dari' => 'Jam mulai', 'izin_berlaku_sampai' => 'Sampai tanggal', 'izin_berlaku_jam_sampai' => 'Jam selesai'] as $field => $label)
        <div>
            <label class="block font-semibold mb-1">{{ $label }}:</label>
            <input type="{{ str_contains($field, 'jam') ? 'time' : 'date' }}" name="{{ $field }}" class="input w-full text-xs" value="{{ old($field, $permit?->$field) }}">
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
                    <input type="text" name="permit_authorizer_name" class="input w-full text-xs text-center" value="{{ old('permit_authorizer_name', $permit?->permit_authorizer_name) }}" placeholder="Nama">
                </td>
                <td class="border px-2 py-2">
                    @if ($permit?->signature_permit_authorizer)
                        <img src="{{ asset($permit->signature_permit_authorizer) }}" class="h-16 mx-auto">
                    @else
                        <button type="button" @click="Alpine.store('signatureModal').openModal('Permit Authorizer')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    @endif
                    <input type="hidden" name="signature_permit_authorizer" value="{{ old('signature_permit_authorizer', $permit?->signature_permit_authorizer) }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="date" name="permit_authorizer_date" class="input w-full text-xs text-center" value="{{ old('permit_authorizer_date', $permit?->permit_authorizer_date) }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="time" name="permit_authorizer_time" class="input w-full text-xs text-center" value="{{ old('permit_authorizer_time', $permit?->permit_authorizer_time) }}">
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
                    <input type="text" name="permit_receiver_name" class="input w-full text-xs text-center" value="{{ old('permit_receiver_name', $permit?->permit_receiver_name) }}" placeholder="Nama">
                </td>
                <td class="border px-2 py-2">
                    @if ($permit?->signature_permit_receiver)
                        <img src="{{ asset($permit->signature_permit_receiver) }}" class="h-16 mx-auto">
                    @else
                        <button type="button" @click="Alpine.store('signatureModal').openModal('Permit Receiver')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    @endif
                    <input type="hidden" name="signature_permit_receiver" value="{{ old('signature_permit_receiver', $permit?->signature_permit_receiver) }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="date" name="permit_receiver_date" class="input w-full text-xs text-center" value="{{ old('permit_receiver_date', $permit?->permit_receiver_date) }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="time" name="permit_receiver_time" class="input w-full text-xs text-center" value="{{ old('permit_receiver_time', $permit?->permit_receiver_time) }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Bagian 10: Penutupan Izin Kerja -->
<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">10. Penutupan Izin Kerja <span class="text-xs font-normal italic float-right">(lingkari)</span></h3>
    <table class="table-auto w-full text-sm border mt-2">
        @php
            $lockTag = old('lock_tag', ($closure?->lock_tag_removed ?? null) ? 'ya' : null);
            $sampahPeralatan = old('sampah_peralatan', ($closure?->equipment_cleaned ?? null) ? 'ya' : null);
            $machineGuarding = old('machine_guarding', ($closure?->guarding_restored ?? null) ? 'ya' : null);
        @endphp

        @foreach ([['lock_tag', 'Lock & Tag sudah dilepas', $lockTag], ['sampah_peralatan', 'Sampah & peralatan sudah dibersihkan', $sampahPeralatan], ['machine_guarding', 'Machine guarding sudah dipasang', $machineGuarding]] as [$name, $label, $checked])
        <tr>
            <td class="border px-2 py-1 italic w-1/4">{{ ucfirst(str_replace('_', ' ', $name)) }}</td>
            <td class="border px-2 py-1">{{ $label }}</td>
            <td class="border px-2 py-1 text-center">Ya <input type="radio" name="{{ $name }}" value="ya" {{ $checked === 'ya' ? 'checked' : '' }}></td>
            <td class="border px-2 py-1 text-center">N/A <input type="radio" name="{{ $name }}" value="na" {{ $checked === 'na' ? 'checked' : '' }}></td>
        </tr>
        @endforeach
    </table>

    <table class="table-auto w-full text-sm border mt-4 text-center">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1">Tanggal:</th>
                <th class="border px-2 py-1">Jam:</th>
                <th class="border px-2 py-1">Tanda Tangan Permit Requestor</th>
                <th class="border px-2 py-1">Tanda Tangan Permit Issuer</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-2">
                    <input type="date" name="penutupan_tanggal" class="input w-full text-xs text-center" value="{{ old('penutupan_tanggal', $closure?->closed_date) }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="time" name="penutupan_jam" class="input w-full text-xs text-center" value="{{ old('penutupan_jam', $closure?->closed_time) }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="text" name="requestor_name" class="input w-full text-xs text-center mb-1" value="{{ old('requestor_name', $closure?->requestor_name) }}" placeholder="Nama">
                    @if ($closure?->requestor_sign)
                        <img src="{{ asset($closure->requestor_sign) }}" class="h-16 mx-auto">
                    @else
                        <button type="button" @click="Alpine.store('signatureModal').openModal('Permit Requestor')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    @endif
                    <input type="hidden" name="requestor_signature" value="{{ old('requestor_signature', $closure?->requestor_sign) }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="text" name="issuer_name" class="input w-full text-xs text-center mb-1" value="{{ old('issuer_name', $closure?->issuer_name) }}" placeholder="Nama">
                    @if ($closure?->issuer_sign)
                        <img src="{{ asset($closure->issuer_sign) }}" class="h-16 mx-auto">
                    @else
                        <button type="button" @click="Alpine.store('signatureModal').openModal('Permit Issuer')" class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    @endif
                    <input type="hidden" name="issuer_signature" value="{{ old('issuer_signature', $closure?->issuer_sign) }}">
                </td>
            </tr>
        </tbody>
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