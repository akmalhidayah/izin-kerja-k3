<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Izin Kerja Khusus</title>
    <style>
        @page { size: A4; margin: 10mm; }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
        }
        .header-table, .table, .checkbox-table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td,
        .checkbox-table th, .checkbox-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }
        .header-table td {
            vertical-align: top;
        }
        .section-title {
            background-color: #000;
            color: #fff;
            font-weight: bold;
            padding: 6px;
            font-size: 12px;
        }
        .label {
            font-weight: bold;
        }
        .logo {
            height: 50px;
        }
    </style>
</head>
<body>
<!-- Header dan Deskripsi Form -->
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 13px;">
    <tr>
        <td style="border: 1px solid black; width: 20%; text-align: center;">
                  <img src="file://{{ public_path('images/logo-st.png') }}" alt="Logo Perusahaan" style="width: 40%; height: auto;">
        </td>
        <td colspan="2" style="border: 1px solid black; text-align: center;">
            <h2 style="margin: 0;">IZIN KERJA</h2>
            <h3 style="margin: 0;">PEKERJAAN PANAS BERISIKO TINGGI</h3>
        </td>
        <td style="border: 1px solid black; width: 25%;">
            <strong>Nomor:</strong> <span style="color: gray;">Jika ada</span>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="border: 1px solid black; font-size: 12px; padding: 5px; text-align: justify;">
            Izin kerja pekerjaan panas berisiko tinggi harus diterbitkan untuk semua pekerjaan panas berisiko tinggi yang mengakibatkan terjadinya kebakaran atau ledakan, termasuk namun tidak terbatas pada pekerjaan panas di area <i>bag plant</i>, <i>raw coal storage</i>, <i>raw coal stockpile</i>, <i>coal mill</i>, <i>fine coal bin</i>, <i>coal transport</i>, laboratorium, tangki limbah cair (BBS tank), tangki penyimpanan IDO (<i>Industrial Diesel Oil</i>), platform AFR (<i>Alternative Fuel Raw Material</i>), <i>AFR storage</i>, <i>explosives storage</i>. Pekerjaan tidak bisa dimulai hingga izin kerja diverifikasi oleh <i>Permit Verificator</i>, diterbitkan oleh <i>Permit Issuer</i>, disahkan oleh <i>Permit Authorizer</i> dan <i>major hazards & control</i> disosialisasikan oleh <i>Permit Receiver</i>.
        </td>
    </tr>
</table>

<!-- Bagian 1 - Detail Pekerjaan -->
{{-- 1. Detail Pekerjaan --}}
<div class="section-title" style="background-color: black; color: white; padding: 5px; font-weight: bold;">1. Detail Pekerjaan</div>

<table class="table" style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 13px;">
    <tr>
        <th style="width: 50%; text-align: left;">Lokasi pekerjaan:</th>
        <th style="width: 50%; text-align: left;">Tanggal:</th>
    </tr>
    <tr>
        <td>{{ $detail->location ?? '-' }}</td>
        <td>{{ $detail->work_date ?? '-' }}</td>
    </tr>
    <tr>
        <th colspan="2" style="text-align: left;">Uraian pekerjaan:</th>
    </tr>
    <tr>
        <td colspan="2">{{ $detail->job_description ?? '-' }}</td>
    </tr>
    <tr>
        <th colspan="2" style="text-align: left;">Peralatan/perlengkapan yang akan digunakan pada pekerjaan:</th>
    </tr>
    <tr>
        <td colspan="2">{{ $detail->equipment ?? '-' }}</td>
    </tr>
    <tr>
        <th style="width: 50%; text-align: left;">Perkiraan jumlah pekerja yang akan terlibat dalam pekerjaan ini:</th>
        <th style="width: 50%; text-align: left;">Nomor gawat darurat yang harus dihubungi saat darurat:</th>
    </tr>
    <tr>
        <td>{{ $detail->worker_count ?? '-' }} Orang</td>
        <td>{{ $detail->emergency_contact ?? '-' }}</td>
    </tr>
</table>

{{-- Bagian 2: Pengukuran Gas (Dinamis) --}}
@php
    $gasResults = json_decode($permit->pengukuran_gas ?? '{}', true);
@endphp

<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px; margin-top: 5px;">
    <thead>
        <tr>
            <th colspan="5" style="background-color: black; color: white; text-align: left; padding: 5px;">
                2. Pengukuran Berkala Kadar Gas di Udara <span style="font-weight: normal;">(jika diperlukan, diisi oleh <i>Permit Verificator</i>)</span>
            </th>
        </tr>
        <tr>
            <th style="border: 1px solid black; font-style: italic; padding: 5px; text-align: center;">Item</th>
            <th style="border: 1px solid black; font-style: italic; padding: 5px; text-align: center;">NAB</th>
            <th style="border: 1px solid black; font-style: italic; padding: 5px; text-align: center;">Tanggal</th>
            <th style="border: 1px solid black; font-style: italic; padding: 5px; text-align: center;">Hasil</th>
            <th style="border: 1px solid black; font-style: italic; padding: 5px; text-align: center;">Jam</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($gasResults as $item => $entries)
            @php
                // Deteksi jika $item = "O2 (19.5% - 23.5%)"
                $split = explode('(', $item);
                $gasName = trim($split[0]);
                $nab = isset($split[1]) ? trim(str_replace(')', '', $split[1])) : '';
            @endphp

            @foreach ($entries as $i => $entry)
                <tr>
                    @if ($i === 0)
                        <td rowspan="{{ count($entries) }}" style="border: 1px solid black; text-align: center;">{{ $gasName }}</td>
                        <td rowspan="{{ count($entries) }}" style="border: 1px solid black; text-align: center;">{!! $nab !!}</td>
                    @endif
                    <td style="border: 1px solid black; text-align: center;">{{ $entry['tgl'] ?? '' }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $entry['hasil'] ?? '' }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $entry['jam'] ?? '' }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

{{-- Bagian 3: Persyaratan Kerja Aman --}}
@php
    $checklist = json_decode($permit->persyaratan_kerja_panas ?? '[]', true);
    $items = [
        'Area kerja sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
        '<i>Area kerja</i> area dibawah pekerjaan panas sudah dibatasi dengan barikade/<i>safety line</i>.',
        'Area kerja sudah diamankan dari potensi jatuhan benda/material.',
        'Area kerja sudah diamankan dari potensi pekerja terpeleset/tersandung.',
        'Area kerja sudah diamankan dari potensi pekerja terjatuh saat pekerjaan panas di ketinggian.',
        '<i>Job Safety Analysis/Safe Working Procedure</i> sudah tersedia dan semua pengendalian bahayanya sudah dilakukan.',
        'Semua pekerja yang terlibat terlatih, memahami bahaya, risiko dan pengendaliannya untuk melakukan pekerjaan panas.',
        'Semua pekerja memahami bahwa dilarang merokok di area kerja panas.',
        'Juru las memiliki sertifikat keahlian yang sesuai dan mengetahui memasang kabel grounding harus ke benda kerja bukan ke struktur lainnya untuk mencegah risiko tersebut.',
        'Pekerja panas dilengkapi dengan <i>personal gas detector</i>.',
        'Semua pekerja yang terlibat sudah menggunakan alat pelindung diri yang sesuai untuk pekerjaan panas.',
        'Ada Fire Sentry yang bisa menggunakan APAR, mengetahui nomor gawat darurat, memahami tanggung jawabnya seperti monitor area pekerjaan, tidak meninggalkan lokasi pekerjaan, memadamkan api yang bisa menyebabkan kebakaran, menghubungi nomor gawat darurat jika api tidak bisa dipadamkan, tetap berada di area kerja melakukan pemeriksaan minimum sampai 30 menit setelah selesai pekerjaan panas.',
        'Peralatan kerja panas yang akan digunakan sudah diperiksa dan dipastikan layak dan aman untuk digunakan.',
        'Sistem pembumian (<i>grounding</i>) pada trafo las telah terpasang dan berfungsi baik.',
        'Trafo las terhubung pada <i>welding point</i> yang dilengkapi dengan ELCB.',
        '<i>Flashback arrestor</i> terpasang pada kedua ujung selang gas bertekanan.',
        'Alat pelindung diri kerja panas yang akan dipakai sudah diperiksa dan dinyatakan layak dan aman untuk dipakai.',
        'Bahan mudah terbakar sudah dijauhkan/disingkirkan dari area kerja (>10 meter)/dipisahkan dengan dinding tahan api.',
        'Bahan mudah terbakar pada <i>pipeline/duct</i>, <i>fine coal bin</i>, tangki limbah cair (BBS tank) tangki penyimpanan IDO (<i>Industrial Diesel Oil</i>), <i>explosives storage</i> atau sejenisnya sudah dikosongkan.',
        'APAR/sarana pemadam api lainnya sudah disediakan untuk pekerjaan panas di area kerja.',
        'Selimut tahan api, perisai panas sudah disediakan untuk mencegah percikan <i>spark</i>.',
        'Tabung gas dan tabung pemotong lainnya sudah diamankan pada struktur yang kuat serta dilindungi dari jatuhan material.',
        'Periksa peralatan kerja untuk menyalakan api gas bertekanan.',
        'Pekerjaan panas di ruang tertutup bukan ventilasi/sirkulasi udaranya baik atau dipasang <i>exhaust fan</i> untuk sirkulasi udara.',
    ];
@endphp

<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                3. Persyaratan Kerja Aman
            </th>
            <th style="background-color: black; color: white; text-align: center; width: 12%;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $i => $text)
        <tr>
            <td style="width: 5%; border: 1px solid black; text-align: center;">•</td>
            <td style="border: 1px solid black; padding: 5px;">{!! $text !!}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $checklist[$i] ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Bagian 4: Rekomendasi --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                4. Rekomendasi Persyaratan Kerja Aman Tambahan dari <i>Permit Verificator/Permit Issuer</i> <span style="font-weight: normal;">(jika ada)</span>
            </th>
            <th style="background-color: black; color: white; text-align: center; width: 12%;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2" style="border: 1px solid black; height: 80px;">{{ $permit->rekomendasi_kerja_aman_tambahan ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $permit->rekomendasi_kerja_aman_setuju ?? 'Ya' }}</td>
        </tr>
    </tbody>
</table>

{{-- Bagian 5: Permohonan Izin Kerja --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                5. Permohonan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px;">
                <i>Permit Requestor:</i><br>
                Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk dapat melakukan pekerjaan ini.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Nama:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanda tangan:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanggal:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black;">{{ $permit->requestor_name ?? '' }}</td>
       <td style="border: 1px solid black;">
    @if($permit->signature_requestor && file_exists(public_path($permit->signature_requestor)))
        <img src="{{ public_path($permit->signature_requestor) }}" style="height: 40px;">
    @endif
</td>

            <td style="border: 1px solid black;">{{ \Carbon\Carbon::parse($permit->requestor_date ?? '')->format('d-m-Y') }}</td>
            <td style="border: 1px solid black;">{{ $permit->requestor_time ?? '' }}</td>
        </tr>
    </tbody>
</table>

{{-- Bagian 6: Verifikasi Izin Kerja --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; margin-top: 10px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                6. Verifikasi Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px;">
                <i>High Risk Hot Work Permit Verificator:</i><br>
                Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman yang telah ditentukan
                dan atau rekomendasi persyaratan kerja aman tambahan dari <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Nama:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanda tangan:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanggal:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black;">{{ $permit->verificator_name ?? '' }}</td>
        <td style="border: 1px solid black;">
    @if($permit->signature_verificator && file_exists(public_path($permit->signature_verificator)))
        <img src="{{ public_path($permit->signature_verificator) }}" style="height: 40px;">
    @endif
</td>

            <td style="border: 1px solid black;">{{ \Carbon\Carbon::parse($permit->verificator_date ?? '')->format('d-m-Y') }}</td>
            <td style="border: 1px solid black;">{{ $permit->verificator_time ?? '' }}</td>
        </tr>
    </tbody>
</table>

{{-- Bagian 7: Penerbitan Izin Kerja --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; margin-top: 10px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                7. Penerbitan Izin Kerja <span style="font-weight: normal;">(Tanda tangan General Manager jika diperlukan)</span>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px;">
                <i>Permit Issuer & Senior Manager:</i><br>
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan
                atau rekomendasi persyaratan kerja aman tambahan dari <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk
                pekerjaan ini dapat dilakukan.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; text-align: center;"><i>Permit Issuer</i></td>
            <td style="border: 1px solid black; text-align: center;"><i>Senior Manager</i></td>
            <td colspan="2" style="border: 1px solid black; text-align: center;"><i>General Manager</i></td>
        </tr>
        <tr>
          <td style="border: 1px solid black;">
    {{ $permit->permit_issuer_name ?? '' }}<br>
    @if($permit->signature_permit_issuer && file_exists(public_path($permit->signature_permit_issuer)))
        <img src="{{ public_path($permit->signature_permit_issuer) }}" style="height: 40px;">
    @endif
</td>
<td style="border: 1px solid black;">
    {{ $permit->senior_manager_name ?? '' }}<br>
    @if($permit->signature_senior_manager && file_exists(public_path($permit->signature_senior_manager)))
        <img src="{{ public_path($permit->signature_senior_manager) }}" style="height: 40px;">
    @endif
</td>
<td colspan="2" style="border: 1px solid black;">
    {{ $permit->general_manager_name ?? '' }}<br>
    @if($permit->signature_general_manager && file_exists(public_path($permit->signature_general_manager)))
        <img src="{{ public_path($permit->signature_general_manager) }}" style="height: 40px;">
    @endif
</td>

        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px;">
                Izin kerja ini berlaku dari tanggal:
                {{ \Carbon\Carbon::parse($permit->izin_berlaku_dari ?? '')->format('d-m-Y') }} jam: {{ $permit->izin_berlaku_jam_dari ?? '' }},
                sampai tanggal: {{ \Carbon\Carbon::parse($permit->izin_berlaku_sampai ?? '')->format('d-m-Y') }} jam: {{ $permit->izin_berlaku_jam_sampai ?? '' }}
            </td>
        </tr>
    </tbody>
</table>

{{-- Bagian 8: Pengesahan Izin Kerja --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; margin-top: 10px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                8. Pengesahan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px;">
                <i>Permit Authorizer:</i><br>
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan
                atau rekomendasi persyaratan kerja aman tambahan dari <i>Permit Verificator/Permit Issuer</i> telah dipenuhi
                untuk dapat melakukan pekerjaan ini serta saya sudah menekankan apa saja <i>major hazards</i> dan
                pengendaliannya yang harus disosialisasikan oleh <i>Permit Receiver</i> kepada seluruh pekerja terkait.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; text-align: center;">Nama:</td>
            <td style="border: 1px solid black; text-align: center;">Tanda tangan:</td>
            <td style="border: 1px solid black; text-align: center;">Tanggal:</td>
            <td style="border: 1px solid black; text-align: center;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black;">{{ $permit->authorizer_name ?? '' }}</td>
         <td style="border: 1px solid black;">
    @if($permit->authorizer_signature && file_exists(public_path($permit->authorizer_signature)))
        <img src="{{ public_path($permit->authorizer_signature) }}" style="height: 40px;">
    @endif
</td>

            <td style="border: 1px solid black;">{{ \Carbon\Carbon::parse($permit->authorizer_date ?? '')->format('d-m-Y') }}</td>
            <td style="border: 1px solid black;">{{ $permit->authorizer_time ?? '' }}</td>
        </tr>
    </tbody>
</table>

{{-- Bagian 9: Pelaksanaan Pekerjaan --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; margin-top: 10px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                9. Pelaksanaan Pekerjaan
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px;">
                <i>Permit Receiver:</i><br>
                Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman 
                tambahan dari <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya 
                sudah mensosialisasikan apa saja <i>major hazards</i> dan pengendaliannya dari pekerjaan ini kepada seluruh pekerja 
                terkait.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Nama:</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Tanda tangan:</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Tanggal:</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black;">{{ $permit->receiver_name ?? '' }}</td>
         <td style="border: 1px solid black;">
    @if($permit->receiver_signature && file_exists(public_path($permit->receiver_signature)))
        <img src="{{ public_path($permit->receiver_signature) }}" style="height: 40px;">
    @endif
</td>

            <td style="border: 1px solid black;">
                {{ \Carbon\Carbon::parse($permit->receiver_date ?? '')->format('d-m-Y') }}
            </td>
            <td style="border: 1px solid black;">{{ $permit->receiver_time ?? '' }}</td>
        </tr>
    </tbody>
</table>


<table class="table">
    <thead>
        <tr>
            <th colspan="3" style="background-color: black; color: white; text-align: left; padding: 5px; font-weight: bold;">
                10. Penutupan Izin Kerja
            </th>
            <th style="width: 10%; background-color: black; color: white; text-align: center;">(ya/na)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="width: 20%; font-style: italic;"><b>Lock & Tag</b></td>
            <td colspan="2">Semua <i>lock & tag</i> sudah dilepas</td>
            <td style="text-align: center;">{{ $closure?->lock_tag_removed ? 'ya' : 'tidak' }}</td>
        </tr>
        <tr>
            <td style="font-style: italic;"><b>Sampah & Peralatan Kerja</b></td>
            <td colspan="2">Semua sampah sudah dibersihkan dan peralatan kerja sudah diamankan</td>
            <td style="text-align: center;">{{ $closure?->equipment_cleaned ? 'ya' : 'tidak' }}</td>
        </tr>
        <tr>
            <td style="font-style: italic;"><b>Machine Guarding</b></td>
            <td colspan="2">Semua <i>machine guarding</i> sudah dipasang kembali</td>
            <td style="text-align: center;">{{ $closure?->guarding_restored ? 'ya' : 'tidak' }}</td>
        </tr>

        {{-- Tanda Tangan --}}
        <tr>
            <th style="width: 25%;">Tanggal:</th>
            <th style="width: 25%;">Jam:</th>
            <th colspan="2" style="text-align: center;">Tanda Tangan</th>
        </tr>
        <tr>
            <td>{{ $closure?->closed_date ?? '-' }}</td>
            <td>{{ $closure?->closed_time ?? '-' }}</td>
            <td style="text-align: center;">
                {{ $closure?->requestor_name ?? '-' }}<br>
                @if($closure?->requestor_sign)
                    <img src="{{ public_path($closure->requestor_sign) }}" style="height: 50px;" alt="TTD">
                @endif
                <div><i>Permit Requestor</i></div>
            </td>
            <td style="text-align: center;">
                {{ $closure?->issuer_name ?? '-' }}<br>
                @if($closure?->issuer_sign)
                    <img src="{{ public_path($closure->issuer_sign) }}" style="height: 50px;" alt="TTD">
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Jumlah RFID yang digunakan</td>
            <td colspan="2" style="text-align: center;">{{ $closure?->jumlah_rfid ?? '-' }}</td>
        </tr>
    </tbody>
</table>

</body>
</html>