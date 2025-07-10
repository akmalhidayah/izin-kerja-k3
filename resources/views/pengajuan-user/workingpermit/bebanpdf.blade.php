<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Izin Kerja Mengangkat Beban</title>
    <style>
        @page { size: A4; margin: 10mm; }
        body {
        font-family: "DejaVu Sans", sans-serif;
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
            text-align: left;
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
{{-- HEADER --}}
<table class="header-table">
    <tr>
        <td style="width: 20%;">
            <img src="{{ public_path('images/logo-st.png') }}" alt="Logo" height="50">
        </td>
        <td style="width: 60%; text-align: center;">
            <h2 style="margin: 0;">IZIN KERJA MENGANGKAT BEBAN</h2>
        </td>
        <td style="width: 20%; text-align: right;">
            <span style="font-weight: bold;">Nomor:</span> <span style="color: gray;">Jika ada</span>
        </td>
    </tr>
</table>

<p style="text-align: justify; font-size: 11px; margin-top: 5px;">
    Izin kerja ini diberikan untuk semua pekerjaan mengangkat beban dengan pesawat angkat bergerak misalnya
    <i>mobile crane</i> dan lain-lain, pengangkatan diluar rutinitas dengan pesawat angkat tetap berenergi seperti
    <i>overhead/hoist crane</i>, <i>hoist winch</i> dan lain-lain, pengangkatan tandem. Pekerjaan tidak bisa dimulai hingga izin kerja
    di verifikasi oleh <i>Permit Verificator</i>, diterbitkan oleh <i>Permit Issuer</i>, disahkan oleh <i>Permit Authorizer</i> dan
    <i>major hazards & control</i> disosialisasikan oleh <i>Permit Receiver</i>.
</p>
<!-- Bagian 1: Detail Pekerjaan -->
<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; text-align: left; padding: 5px;">1. Detail Pekerjaan</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid #000; width: 50%; padding: 5px;"><b>Lokasi pekerjaan:</b><br>{{ $detail->location ?? '-' }}</td>
            <td style="border: 1px solid #000; padding: 5px;"><b>Tanggal:</b><br>{{ \Carbon\Carbon::parse($detail->work_date ?? '')->format('d-m-Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid #000; padding: 5px;"><b>Uraian pekerjaan:</b><br>{{ $detail->job_description ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid #000; padding: 5px;"><b>Peralatan/perlengkapan:</b><br>{{ $detail->equipment ?? '-' }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px;"><b>Jumlah pekerja:</b><br>{{ $detail->worker_count ?? '-' }}</td>
            <td style="border: 1px solid #000; padding: 5px;"><b>Nomor darurat:</b><br>{{ $detail->emergency_contact ?? '-' }}</td>
        </tr>
    </tbody>
</table>

<table class="table">
    <thead>
        <tr>
            <th style="background-color: black; color: white; font-weight: bold;">
                2. Dokumentasi Persyaratan Pengangkatan Beban
            </th>
            <th style="background-color: black; color: white; text-align: center;">Status</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><i>Operator</i> pesawat angkat memiliki lisensi K3 yang masih berlaku dan sesuai (lampirkan).</td>
            <td style="text-align: center;">{{ $permit?->dok_operator ? '✓ Ya' : 'N/A' }}</td>
        </tr>
        <tr>
            <td>Juru ikat (<i>Rigger</i>) memiliki lisensi K3 yang masih berlaku (lampirkan).</td>
            <td style="text-align: center;">{{ $permit?->dok_rigger ? '✓ Ya' : 'N/A' }}</td>
        </tr>
        <tr>
            <td>Pesawat angkat memiliki sertifikat uji kelayakan yang masih berlaku (lampirkan).</td>
            <td style="text-align: center;">{{ $permit?->dok_sertifikat ? '✓ Ya' : 'N/A' }}</td>
        </tr>
        <tr>
            <td><i>Load chart</i> pengangkatan tersedia, di unit <i>control display/hardcopy</i>.</td>
            <td style="text-align: center;">{{ $permit?->dok_loadchart ? '✓ Ya' : 'N/A' }}</td>
        </tr>
        <tr>
            <td>Rencana pengangkatan telah dibuat dan disetujui oleh <i>Lifting Permit Verificator</i>.</td>
            <td style="text-align: center;">{{ $permit?->dok_rencana_pengangkatan ? '✓ Ya' : 'N/A' }}</td>
        </tr>
        <tr>
            <td><i>Job Safety Analysis/Safe Working Procedure</i> sudah tersedia dan pengendalian bahaya dilakukan.</td>
            <td style="text-align: center;">{{ $permit?->dok_jsa ? '✓ Ya' : 'N/A' }}</td>
        </tr>
    </tbody>
</table>
@php
    $statusList = $permit?->persyaratan_kerja_aman ?? [];
    $textList = [
        'Area pengangkatan sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
        'Area pengangkatan sudah dibatasi dengan memasang barricade atau safety line dan rambu peringatan.',
        'Area pengangkatan sudah diamankan dari potensi jatuhan benda/material.',
        'Area pengangkatan sudah diamankan dari potensi pekerja terpleset/tersandung.',
        'Area pengangkatan bebas/dalam jarak aman dari kabel listrik bertegangan tinggi.',
        'Radius kerja pesawat angkat sudah diamankan.',
        'Sudah tersedia standby person untuk mengamankan area pengangkatan dan memahami tanggung jawabnya.',
        'Komunikasi antara Rigger dan Operator menggunakan radio komunikasi/handphone/sinyal tangan.',
        'Operator dan Rigger sudah menentukan kode sinyal tangan sebelum pengangkatan.',
        'Aba-aba pengangkatan dilakukan oleh Rigger yang memiliki lisensi K3 yang masih berlaku.',
        'Helper pekerjaan pengangkatan kompeten terhadap pekerjaan yang dilakukan.',
        'Alat pelindung diri yang akan dipakai sudah diperiksa dan dinyatakan layak untuk dipakai.',
        'Semua pekerja pengangkatan sudah menggunakan alat pelindung diri yang sesuai.',
        'Semua pekerja terkait memahami bahwa dilarang berada dibawah beban yang sedang diangkat.',
        'Semua pekerja terkait memahami dilarang naik pada beban yang sedang diangkat.',
        'Pekerja yang bekerja di dalam man basket, sudah dinyatakan fit untuk bekerja di ketinggian.',
        'Pesawat angkat ditempatkan pada permukaan yang datar, keras dan rata.',
        'Pesawat angkat yang akan digunakan sudah diperiksa ulang dan dipastikan layak pakai.',
        'Semua outrigger telah dikeluarkan maksimal diatas bantalan kayu/besi.',
        'Counterweight pesawat angkat dipasang sebelum pengangkatan dilakukan.',
        'Pesawat angkat dilengkapi dengan limit switch dan berfungsi baik.',
        'Block hook pesawat angkat memiliki safety latch dan berfungsi baik.',
        'Sudah dilakukan load test yang sesuai dengan kapasitas angkat hoist winch.',
        'Alat bantu angkat yang akan digunakan sudah diperiksa dan dipastikan layak untuk digunakan, tertera SWL.',
        'Pasang tali pandu pengarah (tag line) pada beban yang akan diangkat.',
        'Man basket sudah diperiksa, dipastikan layak dan aman untuk digunakan, pintu mengarah kedalam, ada pengunci pintu, ada anchor point untuk hook FBH, ada tag line.',
        'Tertera informasi SWL dan jumlah maksimum orang yang bisa diangkat pada man basket, dan berwarna kontras.',
        'Maksimum hanya 2 pekerja yang boleh diangkat dalam man basket, kecuali ditentukan lain oleh orang yang kompeten.',
        'Remote control hoist/overhead crane area pengangkatan mempunyai wind direction yang jelas.'
    ];
@endphp

<table class="table">
    <thead>
        <tr>
            <th colspan="1" style="background-color: black; color: white; font-weight: bold;">
                3. Persyaratan Kerja Aman
            </th>
            <th style="background-color: black; color: white; text-align: center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($textList as $index => $text)
            <tr>
                <td>{{ $text }}</td>
                <td style="text-align: center;">
                    {{ ($statusList[$index] ?? '') === 'ya' ? '✓ Ya' : 'N/A' }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>


<table class="table">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold;">
                4. Rekomendasi Persyaratan Kerja Aman Tambahan dari <i>Permit Verificator/Permit Issuer</i> (Jika ada)
            </th>
            <th style="background-color: black; color: white; text-align: center;">Status</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2" style="height: 100px;">{{ $permit?->rekomendasi_kerja_aman ?? '-' }}</td>
            <td style="text-align: center;">{{ $permit?->rekomendasi_status === 'ya' ? '✓ Ya' : 'N/A' }}</td>
        </tr>
    </tbody>
</table>

<table class="table">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold;">
                5. Permohonan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="font-style: italic; font-weight: bold;">Permit Requestor:</td>
        </tr>
        <tr>
            <td colspan="4">
                Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk dapat melakukan pekerjaan ini.
            </td>
        </tr>
        <tr style="text-align: center; font-weight: bold;">
            <td>Nama:</td>
            <td>Tanda tangan:</td>
            <td>Tanggal:</td>
            <td>Jam:</td>
        </tr>
        <tr>
            <td style="text-align: center;">{{ $permit?->permit_requestor_name ?? '-' }}</td>
            <td style="text-align: center;">
                @if ($permit?->signature_permit_requestor)
                    <img src="{{ public_path($permit->signature_permit_requestor) }}" height="40" alt="TTD">
                @endif
            </td>
            <td style="text-align: center;">
                {{ $permit?->permit_requestor_date?->format('d-m-Y') ?? '-' }}
            </td>
            <td style="text-align: center;">
                {{ $permit?->permit_requestor_time ?? '-' }}
            </td>
        </tr>
    </tbody>
</table>

{{-- Bagian 6: Verifikasi Izin Kerja --}}
<table class="table">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold;">
                6. Verifikasi Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="font-style: italic; font-weight: bold;">Lifting Permit Verificator:</td>
        </tr>
        <tr>
            <td colspan="4">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua dokumentasi persyaratan pengangkatan beban,
                persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi serta saya telah menyetujui rencana pengangkatan untuk pekerjaan ini dapat dilakukan.
            </td>
        </tr>
        <tr style="text-align: center; font-weight: bold;">
            <td>Nama:</td>
            <td>Tanda tangan:</td>
            <td>Tanggal:</td>
            <td>Jam:</td>
        </tr>
        <tr>
            <td style="text-align: center;">{{ $permit?->verificator_name ?? '' }}</td>
            <td style="text-align: center;">
                @if (!empty($permit?->signature_verificator))
                    <img src="{{ public_path($permit->signature_verificator) }}" alt="TTD" style="height: 40px;">
                @endif
            </td>
            <td style="text-align: center;">{{ $permit?->verificator_date?->format('Y-m-d') ?? '' }}</td>
            <td style="text-align: center;">{{ $permit?->verificator_time ?? '' }}</td>
        </tr>
    </tbody>
</table>

{{-- Bagian 7: Penerbitan Izin Kerja --}}
<table class="table">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold;">
                7. Penerbitan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="font-style: italic; font-weight: bold;">Permit Issuer:</td>
        </tr>
        <tr>
            <td colspan="4">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan/atau rekomendasi persyaratan kerja aman tambahan dari 
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
            </td>
        </tr>
        <tr style="text-align: center; font-weight: bold;">
            <td>Nama:</td>
            <td>Tanda tangan:</td>
            <td>Tanggal:</td>
            <td>Jam:</td>
        </tr>
        <tr>
            <td style="text-align: center;">{{ $permit?->permit_issuer_name ?? '' }}</td>
            <td style="text-align: center;">
                @if (!empty($permit?->signature_permit_issuer))
                    <img src="{{ public_path($permit->signature_permit_issuer) }}" alt="TTD" style="height: 40px;">
                @endif
            </td>
            <td style="text-align: center;">{{ $permit?->permit_issuer_date?->format('Y-m-d') ?? '' }}</td>
            <td style="text-align: center;">{{ $permit?->permit_issuer_time ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="4">
                Izin kerja ini berlaku dari tanggal: <u>{{ $permit?->izin_berlaku_dari?->format('Y-m-d') ?? '/' }}</u>
                jam: <u>{{ $permit?->izin_berlaku_jam_dari ?? '_____' }}</u>
                sampai tanggal: <u>{{ $permit?->izin_berlaku_sampai?->format('Y-m-d') ?? '/' }}</u>
                jam: <u>{{ $permit?->izin_berlaku_jam_sampai ?? '_____' }}</u>
            </td>
        </tr>
    </tbody>
</table>

{{-- Bagian 8: Pengesahan Izin Kerja --}}
<table class="table">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold;">
                8. Pengesahan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="font-style: italic; font-weight: bold;">Permit Authorizer:</td>
        </tr>
        <tr>
            <td colspan="4">
                Saya menyatakan bahwa saya telah memeriksa area kerja, semua dokumentasi persyaratan pengangkatan beban telah dilengkapi,
                semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah menekankan apa saja 
                <i>major hazards</i> dan pengendaliannya yang harus disosialisasikan oleh <i>Permit Receiver</i> kepada seluruh pekerja terkait.
            </td>
        </tr>
        <tr style="text-align: center; font-weight: bold;">
            <td>Nama:</td>
            <td>Tanda tangan:</td>
            <td>Tanggal:</td>
            <td>Jam:</td>
        </tr>
        <tr>
            <td style="text-align: center;">{{ $permit?->permit_authorizer_name ?? '' }}</td>
            <td style="text-align: center;">
                @if (!empty($permit?->signature_permit_authorizer))
                    <img src="{{ public_path($permit->signature_permit_authorizer) }}" alt="TTD" style="height: 40px;">
                @endif
            </td>
            <td style="text-align: center;">{{ $permit?->permit_authorizer_date?->format('Y-m-d') ?? '' }}</td>
            <td style="text-align: center;">{{ $permit?->permit_authorizer_time ?? '' }}</td>
        </tr>
    </tbody>
</table>

{{-- Bagian 9: Pelaksanaan Pekerjaan --}}
<table class="table">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold;">
                9. Pelaksanaan Pekerjaan
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="font-style: italic; font-weight: bold;">Permit Receiver:</td>
        </tr>
        <tr>
            <td colspan="4">
                Saya menyatakan bahwa semua dokumentasi persyaratan pengangkatan beban telah dilengkapi,
                semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah mensosialisasikan apa saja 
                <i>major hazards</i> dan pengendaliannya dari pekerjaan ini kepada seluruh pekerja terkait.
            </td>
        </tr>
        <tr style="text-align: center; font-weight: bold;">
            <td>Nama:</td>
            <td>Tanda tangan:</td>
            <td>Tanggal:</td>
            <td>Jam:</td>
        </tr>
        <tr>
            <td style="text-align: center;">{{ $permit?->permit_receiver_name ?? '' }}</td>
            <td style="text-align: center;">
                @if (!empty($permit?->signature_permit_receiver))
                    <img src="{{ public_path($permit->signature_permit_receiver) }}" alt="TTD" style="height: 40px;">
                @endif
            </td>
            <td style="text-align: center;">{{ $permit?->permit_receiver_date?->format('Y-m-d') ?? '' }}</td>
            <td style="text-align: center;">{{ $permit?->permit_receiver_time ?? '' }}</td>
        </tr>
    </tbody>
</table>


<!-- Bagian 10: Penutupan Izin Kerja -->
<table class="table" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th colspan="3" style="background-color: black; color: white; font-weight: bold; padding: 4px;">
                10. Penutupan Izin Kerja
            </th>
            <th style="width: 10%; background-color: black; color: white; text-align: center;">(Beri Centang)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="width: 25%; font-style: italic;"><b>Lock & Tag</b></td>
            <td colspan="2">Semua <i>lock & tag</i> sudah dilepas</td>
            <td style="text-align: center;">{{ $closure?->lock_tag_removed ? '✓ Ya' : 'N/A' }}</td>
        </tr>
        <tr>
            <td style="font-style: italic;"><b>Sampah & Peralatan Kerja</b></td>
            <td colspan="2">Semua sampah sudah dibersihkan dan peralatan kerja sudah diamankan</td>
            <td style="text-align: center;">{{ $closure?->equipment_cleaned ? '✓ Ya' : 'N/A' }}</td>
        </tr>
        <tr>
            <td style="font-style: italic;"><b>Machine Guarding</b></td>
            <td colspan="2">Semua <i>machine guarding</i> sudah dipasang kembali</td>
            <td style="text-align: center;">{{ $closure?->guarding_restored ? '✓ Ya' : 'N/A' }}</td>
        </tr>
        <tr>
            <th style="width: 20%;">Tanggal:</th>
            <th style="width: 20%;">Jam:</th>
            <th colspan="2" style="text-align: center;">Tanda Tangan</th>
        </tr>
        <tr style="height: 40px;">
            <td style="text-align: center;">{{ $closure?->closed_date }}</td>
            <td style="text-align: center;">{{ $closure?->closed_time }}</td>
            <td style="text-align: center;">
                @if ($closure?->requestor_sign)
                    <img src="{{ public_path($closure->requestor_sign) }}" height="40" alt="TTD">
                @endif
                <div><i>Permit Requestor</i></div>
            </td>
            <td style="text-align: center;">
                @if ($closure?->issuer_sign)
                    <img src="{{ public_path($closure->issuer_sign) }}" height="40" alt="TTD">
                @endif
                <div><i>Permit Issuer</i></div>
            </td>
        </tr>
    </tbody>
</table>
</body>
</html>