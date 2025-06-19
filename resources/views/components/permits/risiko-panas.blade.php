<form method="POST" action="{{ route('working-permit.risiko-panas.store') }}">
    @csrf
<input type="hidden" name="notification_id" value="{{ $notification->id ?? '' }}">

    <!-- Bagian 1: Detail Pekerjaan -->
    <div class="text-center mb-4">
        <h2 class="text-2xl font-bold uppercase">IZIN KERJA</h2>
        <h3 class="text-xl font-semibold text-gray-700">PEKERJAAN PANAS BERISIKO TINGGI</h3>
        <p class="text-sm mt-2 text-gray-600">
           Izin kerja pekerjaan panas berisiko tinggi harus diterbitkan untuk semua pekerjaan panas berisiko tinggi
           yang mengakibatkan terjadinya kebakaran atau ledakan, termasuk namun tidak terbatas pada pekerjaan panas
           di area bag plant, raw coal storage, raw coal stockpile, coal mill, fine coal bin, coal transport, laboratorium,
           tangki limbah cair (BBS tank)  tangki penyimpanan IDO (Industrial Diesel Oil), platform AFR (Alternative Fuel Raw Material),
           AFR storage, explosives storage. Pekerjaan tidak bisa dimulai hingga izin kerja diverifikasi oleh Permit Verificator,
           diterbitkan oleh Permit Issuer, disahkan oleh Permit Authorizer dan major hazards & control disosialisasikan oleh Permit Receiver.
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
    $pengukuranGas = old('pengukuran_gas', $permit?->pengukuran_gas ?? [
        'O2 (19.5% - 23.5%)' => [],
        'LEL (< 5%)' => [],
        'CO (≤ 25ppm)' => [],
        'H2S (≤ 1ppm)' => [],
        'O3 (≤ 0.2ppm)' => [],
    ]);
    $persyaratanChecked = old('persyaratan_kerja_panas', $permit?->persyaratan_kerja_panas ?? []);
@endphp
@php
    $raw = old('pengukuran_gas') ?? $permit->pengukuran_gas ?? [];

    if (is_string($raw)) {
        $pengukuranGas = json_decode($raw, true) ?? [];
    } else {
        $pengukuranGas = $raw;
    }

    $defaultGas = [
        'O2 (19.5% - 23.5%)',
        'LEL (< 5%)',
        'CO (≤ 25ppm)',
        'H2S (≤ 1ppm)',
        'O3 (≤ 0.2ppm)',
    ];

    foreach ($defaultGas as $key) {
        if (!isset($pengukuranGas[$key]) || !is_array($pengukuranGas[$key])) {
            $pengukuranGas[$key] = [];
        }
    }

    $jsonGas = json_encode($pengukuranGas);
@endphp

{{-- BAGIAN 2 - Pengukuran Gas --}}
<div 
    x-data="{
        defaultGas: ['O2 (19.5% - 23.5%)', 'LEL (< 5%)', 'CO (≤ 25ppm)', 'H2S (≤ 1ppm)', 'O3 (≤ 0.2ppm)'],
        rawData: JSON.parse(@js($jsonGas)),
        dataGas: {},
        init() {
            this.defaultGas.forEach(gas => {
                this.dataGas[gas] = this.rawData[gas] ?? [];
            });
        },
        addRow(gas) {
            this.dataGas[gas].push({ tgl: '', hasil: '', jam: '', sign: '' });
        }
    }"
    x-init="init()"
    class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto text-sm"
>
    <h3 class="font-bold bg-black text-white px-2 py-1">
        3. Pengukuran Berkala Kadar Gas di Udara 
        <span class="text-xs font-normal italic">(diisi oleh <strong>Permit Verificator</strong>, bisa dalam lampiran terpisah)</span>
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
                        <th class="border px-2 py-1">Tanda Tangan</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, index) in rows" :key="index">
                        <tr>
                            <td class="border px-2 py-1">
                                <input type="date" x-model="row.tgl" :name="'pengukuran_gas[' + gas + '][' + index + '][tgl]'" class="input w-full text-xs">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="text" x-model="row.hasil" :name="'pengukuran_gas[' + gas + '][' + index + '][hasil]'" class="input w-full text-xs">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="time" x-model="row.jam" :name="'pengukuran_gas[' + gas + '][' + index + '][jam]'" class="input w-full text-xs">
                            </td>
                            <td class="border px-2 py-1 text-center">
                                <button type="button"
                                    @click="Alpine.store('signatureModal').openModal(gas + ' - ' + (index + 1))"
                                    class="text-blue-600 underline text-xs">TTD</button>
                                <input type="hidden" :name="'pengukuran_gas[' + gas + '][' + index + '][sign]'" x-model="row.sign">
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <button type="button" @click="addRow(gas)"
                class="mt-2 bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">
                + Tambah Baris
            </button>
        </div>
    </template>
</div>

{{-- BAGIAN 3 - Persyaratan Kerja Aman --}}
@php
    $persyaratanKerjaPanas = [
        'Area kerja sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
        'Area kerja/area di bawah pekerjaan panas sudah dibatasi dengan barricade/safety line.',
        'Area kerja sudah diamankan dari potensi jatuhan benda/material.',
        'Area kerja sudah diamankan dari potensi pekerja terpleset/tersandung.',
        'Area kerja sudah diamankan dari potensi pekerja terjatuh saat pekerjaan panas di ketinggian.',
        '<em>Job Safety Analysis/Safe Working Procedure</em> sudah tersedia dan semua pengendalian bahayanya sudah dilakukan.',
        'Semua pekerja yang terlibat terlatih, memahami bahaya, risiko dan pengendaliannya untuk melakukan pekerjaan panas.',
        'Semua pekerja memahami bahwa dilarang merokok di area kerja panas.',
        'Juru las memiliki sertifikat keahlian yang sesuai dan mengetahui memasang kabel grounding harus ke benda kerja bukan ke struktur lainnya untuk mencegah risiko tersebut.',
        'Pekerja panas dilengkapi dengan <em>personal gas detector</em>.',
        'Semua pekerja yang terlibat sudah menggunakan alat pelindung diri yang sesuai untuk pekerjaan panas.',
        'Ada Fire Sentry yang bisa menggunakan APAR, mengerti prosedur gawat darurat, memahami tanggung jawabnya, tetap berada di area kerja sampai 30 menit setelah pekerjaan selesai.',
        'Peralatan kerja panas yang akan digunakan sudah diperiksa dan dipastikan layak dan aman untuk digunakan.',
        'Sistem pembumian (<em>grounding</em>) pada trafo las telah terpasang dan berfungsi baik.',
        'Trafo las terhubung pada <em>welding point</em> yang dilengkapi dengan ELCB.',
        '<em>Flashback arrestor</em> terpasang pada kedua ujung selang gas tekanan.',
        'Alat pelindung diri kerja panas sudah diperiksa dan dinyatakan layak.',
        'Bahan mudah terbakar sudah dipindahkan/disingkirkan dari area kerja/dipisahkan dengan dinding tahan api.',
        'Bahan mudah terbakar pada <em>pipeline/duct, fine coal bin</em>, dll sudah dikosongkan.',
        'APAR/sarana pemadam api lainnya sudah disediakan.',
        'Selimut tahan api/perisai panas sudah disediakan.',
        'Tabung gas diletakkan berdiri dan tetap pada struktur kuat.',
        'Tersedia pemantik api untuk gas bertekanan.',
        'Jika di ruang tertutup: dipasang <em>exhaust fan</em> untuk sirkulasi udara.'
    ];

    $persyaratanChecked = old('persyaratan_kerja_panas') ?? json_decode($permit->persyaratan_kerja_panas ?? '[]', true);
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        3. Persyaratan Kerja Aman <span class="text-xs font-normal italic">(lingkari)</span>
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
            @foreach ($persyaratanKerjaPanas as $index => $persyaratan)
                <tr>
                    <td class="border px-2 py-1">• {!! $persyaratan !!}</td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="persyaratan_kerja_panas[{{ $index }}]" value="ya"
                            {{ ($persyaratanChecked[$index] ?? '') === 'ya' ? 'checked' : '' }}>
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <input type="radio" name="persyaratan_kerja_panas[{{ $index }}]" value="na"
                            {{ ($persyaratanChecked[$index] ?? '') === 'na' ? 'checked' : '' }}>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- bagian 4 rekomendasi persyaratan -->
@php
    $rekomendasiTambahan = old('rekomendasi_kerja_aman_tambahan', $permit->rekomendasi_kerja_aman_tambahan ?? '');
    $rekomendasiSetuju = old('rekomendasi_kerja_aman_setuju', $permit->rekomendasi_kerja_aman_setuju ?? '');
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        4. Rekomendasi Persyaratan Kerja Aman Tambahan dari 
        <em>Permit Verificator/Permit Issuer</em>
        <span class="text-xs font-normal">(jika ada)</span>
    </h3>

    <div class="border border-t-0 border-gray-300 h-24">
        <textarea name="rekomendasi_kerja_aman_tambahan" class="w-full h-full p-2 text-xs border-none resize-none"
            placeholder="Tulis jika ada rekomendasi tambahan...">{{ $rekomendasiTambahan }}</textarea>
    </div>

    <table class="table-auto w-full border mt-1">
        <tr>
            <td class="border px-2 py-1 text-right w-full text-sm">Ya</td>
            <td class="border px-2 py-1 text-center w-16">
                <input type="radio" name="rekomendasi_kerja_aman_setuju" value="ya"
                    {{ $rekomendasiSetuju === 'ya' ? 'checked' : '' }}>
            </td>
        </tr>
    </table>
</div>


<!-- bagian 5 permohonan izin kerja -->
@php
    $requestor_name = old('requestor_name', $permit->requestor_name ?? '');
    $requestor_date = old('requestor_date', $permit->requestor_date ?? '');
    $requestor_time = old('requestor_time', $permit->requestor_time ?? '');
    $signature_requestor = old('signature_requestor', $permit->signature_requestor ?? '');
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">5. Permohonan Izin Kerja</h3>

    <div class="border border-t-0 border-gray-300 p-3 bg-gray-50">
        <p class="italic font-semibold">Permit Requestor:</p>
        <p class="text-sm mt-1">
            Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi tambahan telah dipenuhi.
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
                <td class="border px-2 py-1 text-center">
                    <input type="text" name="requestor_name" value="{{ $requestor_name }}" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-1 text-center">
                    <button type="button"
                        @click="Alpine.store('signatureModal').openModal('Permit Requestor')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_requestor" value="{{ $signature_requestor }}">
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="date" name="requestor_date" value="{{ $requestor_date }}" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="time" name="requestor_time" value="{{ $requestor_time }}" class="input w-full text-xs text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- bagian 6 verfikasi izin kerja -->
@php
    $verificator_name = old('verificator_name', $permit->verificator_name ?? '');
    $verificator_signature = old('signature_verificator', $permit->signature_verificator ?? '');
    $verificator_date = old('verificator_date', $permit->verificator_date ?? '');
    $verificator_time = old('verificator_time', $permit->verificator_time ?? '');
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">6. Verifikasi Izin Kerja</h3>

    <div class="border border-t-0 border-gray-300 p-3 bg-gray-50">
        <p class="italic font-semibold">High Risk Hot Work Permit Verificator:</p>
        <p class="text-sm mt-1">
            Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman dan/atau rekomendasi tambahan dari
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
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
                <td class="border px-2 py-1 text-center">
                    <input type="text" name="verificator_name" value="{{ $verificator_name }}" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-1 text-center">
                    <button 
                        type="button"
                        @click="Alpine.store('signatureModal').openModal('High Risk Verificator')"
                        class="text-blue-600 underline text-xs">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="signature_verificator" value="{{ $verificator_signature }}">
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="date" name="verificator_date" value="{{ $verificator_date }}" class="input w-full text-xs text-center">
                </td>
                <td class="border px-2 py-1 text-center">
                    <input type="time" name="verificator_time" value="{{ $verificator_time }}" class="input w-full text-xs text-center">
                </td>
            </tr>
        </tbody>
    </table>
</div>


<!-- bagian 7 penerbitan izin kerja -->
@php
    $permit_issuer_name = old('permit_issuer_name', $permit->permit_issuer_name ?? '');
    $permit_issuer_signature = old('permit_issuer_signature', $permit->permit_issuer_signature ?? '');

    $senior_manager_name = old('senior_manager_name', $permit->senior_manager_name ?? '');
    $senior_manager_signature = old('senior_manager_signature', $permit->senior_manager_signature ?? '');

    $general_manager_name = old('general_manager_name', $permit->general_manager_name ?? '');
    $general_manager_signature = old('general_manager_signature', $permit->general_manager_signature ?? '');

    $izin_dari = old('izin_berlaku_dari', $permit->izin_berlaku_dari ?? '');
    $izin_jam_dari = old('izin_berlaku_jam_dari', $permit->izin_berlaku_jam_dari ?? '');
    $izin_sampai = old('izin_berlaku_sampai', $permit->izin_berlaku_sampai ?? '');
    $izin_jam_sampai = old('izin_berlaku_jam_sampai', $permit->izin_berlaku_jam_sampai ?? '');
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow mt-6 text-xs">
    <h3 class="font-bold bg-black text-white px-2 py-1">
        7. Penerbitan Izin Kerja 
        <span class="text-xs font-normal italic">(Tanda tangan General Manager jika diperlukan)</span>
    </h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="italic font-semibold mb-2">Permit Issuer & Senior Manager:</p>
        <p>
            Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan/atau rekomendasi tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
        </p>
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
                <td class="border px-2 py-2">
                    <input type="text" name="permit_issuer_name" class="input w-full text-center mb-1 text-xs" placeholder="Nama" value="{{ $permit_issuer_name }}">
                    <button type="button"
                        @click="Alpine.store('signatureModal').openModal('Permit Issuer')"
                        class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    <input type="hidden" name="permit_issuer_signature" value="{{ $permit_issuer_signature }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="text" name="senior_manager_name" class="input w-full text-center mb-1 text-xs" placeholder="Nama" value="{{ $senior_manager_name }}">
                    <button type="button"
                        @click="Alpine.store('signatureModal').openModal('Senior Manager')"
                        class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    <input type="hidden" name="senior_manager_signature" value="{{ $senior_manager_signature }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="text" name="general_manager_name" class="input w-full text-center mb-1 text-xs" placeholder="Nama" value="{{ $general_manager_name }}">
                    <button type="button"
                        @click="Alpine.store('signatureModal').openModal('General Manager')"
                        class="text-blue-600 underline text-xs">Tanda Tangan</button>
                    <input type="hidden" name="general_manager_signature" value="{{ $general_manager_signature }}">
                </td>
            </tr>
        </tbody>
    </table>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-4 text-xs">
        <div>
            <label class="block font-semibold mb-1">Dari tanggal:</label>
            <input type="date" name="izin_berlaku_dari" class="input w-full text-xs" value="{{ $izin_dari }}">
        </div>
        <div>
            <label class="block font-semibold mb-1">Jam mulai:</label>
            <input type="time" name="izin_berlaku_jam_dari" class="input w-full text-xs" value="{{ $izin_jam_dari }}">
        </div>
        <div>
            <label class="block font-semibold mb-1">Sampai tanggal:</label>
            <input type="date" name="izin_berlaku_sampai" class="input w-full text-xs" value="{{ $izin_sampai }}">
        </div>
        <div>
            <label class="block font-semibold mb-1">Jam selesai:</label>
            <input type="time" name="izin_berlaku_jam_sampai" class="input w-full text-xs" value="{{ $izin_jam_sampai }}">
        </div>
    </div>
</div>

<!-- bagian 8 pengesahan izin kerja -->
@php
    $authorizer_name = old('authorizer_name', $permit->authorizer_name ?? '');
    $authorizer_signature = old('authorizer_signature', $permit->authorizer_signature ?? '');
    $authorizer_date = old('authorizer_date', $permit->authorizer_date ?? '');
    $authorizer_time = old('authorizer_time', $permit->authorizer_time ?? '');
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">8. Pengesahan Izin Kerja</h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="text-sm italic font-semibold mb-2">Permit Authorizer:</p>
        <p class="text-sm">
            Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman dan/atau rekomendasi tambahan dari <em>Permit Verificator/Permit Issuer</em> telah dipenuhi, serta saya sudah menekankan apa saja <em>major hazards</em> dan pengendaliannya yang harus disosialisasikan oleh <em>Permit Receiver</em>.
        </p>
    </div>

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100 text-center font-semibold">
            <tr>
                <th class="border px-2 py-1">Nama:</th>
                <th class="border px-2 py-1">Tanda tangan:</th>
                <th class="border px-2 py-1">Tanggal:</th>
                <th class="border px-2 py-1">Jam:</th>
            </tr>
        </thead>
        <tbody class="text-center text-xs">
            <tr>
                <td class="border px-2 py-2">
                    <input type="text" name="authorizer_name" class="input w-full text-xs text-center" placeholder="Nama" value="{{ $authorizer_name }}">
                </td>
                <td class="border px-2 py-2">
                    <button 
                        type="button" 
                        class="text-blue-600 underline text-xs"
                        @click="Alpine.store('signatureModal').openModal('Permit Authorizer')">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="authorizer_signature" value="{{ $authorizer_signature }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="date" name="authorizer_date" class="input w-full text-xs text-center" value="{{ $authorizer_date }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="time" name="authorizer_time" class="input w-full text-xs text-center" value="{{ $authorizer_time }}">
                </td>
            </tr>
        </tbody>
    </table>
</div>


<!-- bagian 9 pelaksanaan pekerjaan -->
@php
    $receiver_name = old('receiver_name', $permit->receiver_name ?? '');
    $receiver_signature = old('receiver_signature', $permit->receiver_signature ?? '');
    $receiver_date = old('receiver_date', $permit->receiver_date ?? '');
    $receiver_time = old('receiver_time', $permit->receiver_time ?? '');
@endphp

<div class="border border-gray-800 rounded-md p-4 bg-white shadow overflow-x-auto mt-6 text-sm">
    <h3 class="font-bold bg-black text-white px-2 py-1">9. Pelaksanaan Pekerjaan</h3>

    <div class="border border-t-0 border-gray-300 p-3">
        <p class="text-sm italic font-semibold mb-2">Permit Receiver:</p>
        <p class="text-sm">
            Saya menyatakan bahwa semua persyaratan kerja aman dan/atau rekomendasi tambahan dari <em>Permit Verificator/Permit Issuer</em> telah dipenuhi dan saya telah mensosialisasikan <em>major hazards</em> dan pengendaliannya kepada seluruh pekerja terkait.
        </p>
    </div>

    <table class="table-auto w-full text-sm border mt-3">
        <thead class="bg-gray-100 text-center font-semibold">
            <tr>
                <th class="border px-2 py-1">Nama:</th>
                <th class="border px-2 py-1">Tanda tangan:</th>
                <th class="border px-2 py-1">Tanggal:</th>
                <th class="border px-2 py-1">Jam:</th>
            </tr>
        </thead>
        <tbody class="text-center text-xs">
            <tr>
                <td class="border px-2 py-2">
                    <input type="text" name="receiver_name" class="input w-full text-xs text-center" placeholder="Nama" value="{{ $receiver_name }}">
                </td>
                <td class="border px-2 py-2">
                    <button 
                        type="button" 
                        class="text-blue-600 underline text-xs"
                        @click="Alpine.store('signatureModal').openModal('Permit Receiver')">
                        Tanda Tangan
                    </button>
                    <input type="hidden" name="receiver_signature" value="{{ $receiver_signature }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="date" name="receiver_date" class="input w-full text-xs text-center" value="{{ $receiver_date }}">
                </td>
                <td class="border px-2 py-2">
                    <input type="time" name="receiver_time" class="input w-full text-xs text-center" value="{{ $receiver_time }}">
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