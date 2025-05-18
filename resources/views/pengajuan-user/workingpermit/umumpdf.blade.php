<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Izin Kerja Umum</title>
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

{{-- Header --}}
<table class="header-table">
    <tr>
        <td style="width: 20%;">
         <img src="file://{{ public_path('images/logo-st.png') }}" class="logo" alt="Logo">

        </td>
        <td style="width: 60%; text-align: center;">
            <h2 style="margin: 0;">IZIN KERJA UMUM</h2>
        </td>
        <td style="width: 20%; text-align: right;">
            <span class="label">Nomor:</span> <span style="color:gray;">-</span>
        </td>
    </tr>
</table>

{{-- Deskripsi --}}
<p style="text-align: justify; margin-top: 5px;">
    Izin kerja ini diberikan untuk pekerjaan tidak rutin yang berisiko menyebabkan kecelakaan atau pekerjaan rutin
    yang ditetapkan dengan izin kerja misalnya pekerjaan isolasi dan penguncian, pekerjaan panas di luar
    <i>Hot Work Designated Area</i>, dan lain-lain.
</p>

{{-- 1. Detail Pekerjaan --}}
<div class="section-title">1. Detail Pekerjaan</div>
<table class="table">
    <tr>
        <th style="width: 50%;">Lokasi pekerjaan:</th>
        <th style="width: 50%;">Tanggal:</th>
    </tr>
    <tr>
        <td>{{ $detail->location ?? '-' }}</td>
        <td>{{ $detail->work_date ?? '-' }}</td>
    </tr>
    <tr>
        <th colspan="2">Uraian pekerjaan:</th>
    </tr>
    <tr>
        <td colspan="2">{{ $detail->job_description ?? '-' }}</td>
    </tr>
    <tr>
        <th colspan="2">Peralatan/perlengkapan yang akan digunakan pada pekerjaan:</th>
    </tr>
    <tr>
        <td colspan="2">{{ $detail->equipment ?? '-' }}</td>
    </tr>
    <tr>
        <th style="width: 50%;">Perkiraan jumlah pekerja yang akan terlibat dalam pekerjaan ini:</th>
        <th style="width: 50%;">Nomor gawat darurat yang harus dihubungi saat darurat:</th>
    </tr>
    <tr>
        <td>{{ $detail->worker_count ?? '-' }} Orang</td>
        <td>{{ $detail->emergency_contact ?? '-' }}</td>
    </tr>
</table>

{{-- Izin Kerja Khusus --}}
@php
    $check = '<span style="font-family: DejaVu Sans; font-weight: bold;">✓</span>';
    $specials = json_decode($permit->izin_khusus ?? '[]', true);
@endphp

<div class="section-title">
    Apakah diperlukan Izin Kerja Khusus untuk pekerjaan ini?
    <span style="font-weight: normal; font-size: 10px;">
        (beri tanda centang ✓ di Izin Kerja Khusus yang diperlukan)
    </span>
</div>

<table class="checkbox-table">
    <tr>
        <td>@if(in_array('panas', $specials)) {!! $check !!} @endif Pekerjaan panas berisiko tinggi</td>
        <td>@if(in_array('ketinggian', $specials)) {!! $check !!} @endif Bekerja di ketinggian ≥ 1.8 meter</td>
    </tr>
    <tr>
        <td>@if(in_array('perancah', $specials)) {!! $check !!} @endif Perancah</td>
        <td>@if(in_array('beban', $specials)) {!! $check !!} @endif Mengangkat beban</td>
    </tr>
    <tr>
        <td>@if(in_array('ruang_terbatas', $specials)) {!! $check !!} @endif Bekerja di ruang tertutup terbatas</td>
        <td>@if(in_array('penggalian', $specials)) {!! $check !!} @endif Penggalian ≥ 30 cm</td>
    </tr>
    <tr>
        <td>@if(in_array('air', $specials)) {!! $check !!} @endif Bekerja di air</td>
        <td>@if(in_array('gaspanas', $specials)) {!! $check !!} @endif Material/gas panas ≥ 150°C</td>
    </tr>
</table>
{{-- 2. Titik Isolasi dan Penguncian jika Diperlukan --}}
<div class="section-title">
    2. Titik Isolasi dan Penguncian jika Diperlukan
    <span style="font-weight: normal; font-size: 10px;">
        (diisi oleh <i>Isolation Officer</i>, bisa melampirkan <i>checklist isolation points</i>)
    </span>
</div>

{{-- Isolasi Energi Listrik --}}
<p style="font-weight: bold;">Isolasi energi listrik</p>
@php
    $isolasiListrik = json_decode($permit->isolasi_listrik ?? '[]', true);
@endphp
<table class="table">
    <thead>
        <tr>
            <th style="width: 20%;">Peralatan / Mesin</th>
            <th style="width: 20%;">Nomor peralatan/mesin</th>
            <th style="width: 25%;">Tempat Isolasi dan penguncian</th>
            <th style="width: 10%;">Locked, Tagged<br>(Ya / Tidak)</th>
            <th style="width: 10%;">Tested<br>(Ya / Tidak)</th>
            <th style="width: 15%;">Tanda Tangan<br><i>Isolation Officer</i></th>
        </tr>
    </thead>
    <tbody>
      @foreach($isolasiListrik as $item)
<tr>
    <td style="height: 25px;">{{ $item['peralatan'] ?? '' }}</td>
    <td>{{ $item['nomor'] ?? '' }}</td>
    <td>{{ $item['tempat'] ?? '' }}</td>
    <td>{{ $item['locked'] ?? '' }}</td>
    <td>{{ $item['tested'] ?? '' }}</td>
    <td></td>
</tr>
@endforeach

    </tbody>
</table>

{{-- Isolasi Energi Non Listrik --}}
<p style="font-weight: bold;">
    Isolasi energi non listrik
    <span style="font-weight: normal; font-style: italic; font-size: 10px;">
        (Thermal, chemical, radiation, potential, gravitational, mechanical dan lain-lain)
    </span>
</p>
@php
    $isolasiNon = json_decode($permit->isolasi_non_listrik ?? '[]', true);
@endphp
<table class="table">
    <thead>
        <tr>
            <th style="width: 20%;">Peralatan / sumber energi</th>
            <th style="width: 20%;">Jenis<br>(Hose, Valve dll)</th>
            <th style="width: 25%;">Tempat isolasi dan penguncian</th>
            <th style="width: 10%;">Locked, Tagged<br>(Ya / Tidak)</th>
            <th style="width: 10%;">Tested<br>(Ya / Tidak)</th>
            <th style="width: 15%;">Tanda Tangan<br><i>Isolation Officer</i></th>
        </tr>
    </thead>
    <tbody>
      @foreach($isolasiNon as $item)
<tr>
    <td style="height: 25px;">{{ $item['peralatan'] ?? '' }}</td>
    <td>{{ $item['jenis'] ?? '' }}</td>
    <td>{{ $item['tempat'] ?? '' }}</td>
    <td>{{ $item['locked'] ?? '' }}</td>
    <td>{{ $item['tested'] ?? '' }}</td>
    <td></td>
</tr>
@endforeach

    </tbody>
</table>
{{-- 3. Persyaratan Kerja Aman --}}
<div class="section-title">3. Persyaratan Kerja Aman</div>
<table class="table">
    <thead>
        <tr>
            <th style="width: 70%;">✔ Persyaratan</th>
            <th style="width: 15%; text-align: center;">Pemohon Kerja (O)</th>
            <th style="width: 15%; text-align: center;">Penerbit Kerja (V)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $items = [
                'Area kerja sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
                'Area kerja sudah dibatasi dengan memasang barikade atau safety line.',
                'Area kerja sudah diamankan dari potensi jatuhan benda/material.',
                'Area kerja sudah diamankan dari potensi pekerja terpleset/tersandung.',
                'Pekerja yang melakukan pekerjaan kompeten terhadap pekerjaan yang dilakukan.',
                '<i>Job Safety Analysis/Safe Working Procedure</i> sudah tersedia dan semua pengendalian bahayanya sudah dilakukan.',
                'Peralatan/perlengkapan kerja yang akan digunakan sudah diperiksa dan dipastikan layak dan aman untuk digunakan.',
                'Alat pelindung diri yang akan dipakai sudah diperiksa dan dinyatakan layak dan aman untuk dipakai.',
                'Semua pekerja yang terlibat sudah menggunakan alat pelindung diri yang sesuai.',
                'Bahan/material yang akan dipakai/dikerjakan sudah diperiksa dan dinyatakan aman.',
                'Bahan mudah terbakar sudah dipindahkan/disingkirkan dari area kerja (>10 meter).',
                'APAR/sarana pemadam api lainnya sudah disediakan untuk pekerjaan panas di area kerja.',
                'Selimut tahan api, perisai panas sudah disediakan dan area di bawah pekerjaan panas sudah dibatasi dengan barikade/safety line.',
                'Ada <i>Fire Sentry</i> untuk pekerjaan panas yang akan dilakukan.',
                'Semua peralatan dan permesinan sudah diblokir, dikunci, diuji dan ditandai oleh <i>Isolation Officer</i>.',
                'Semua pekerja sudah memasang personal lock pada titik isolasi yang sudah ditentukan.'
            ];
        @endphp

        @foreach($items as $text)
        <tr>
            <td>{!! $text !!}</td>
            <td style="text-align: center;">
                <span style="margin-right: 10px;">☑ Ya</span> <span>□ N/A</span>
            </td>
            <td style="text-align: center;">
                <span style="margin-right: 10px;">☑</span> 
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{-- 4. Rekomendasi Persyaratan Kerja Aman Tambahan dari Permit Issuer --}}
@php
    $rekomendasi = $permit->rekomendasi_tambahan ?? '-';
    $status = $permit->rekomendasi_status ?? null;
    $check = '<span style="font-family: DejaVu Sans;">☑</span>';
@endphp

<table class="table">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold;">
                4. Rekomendasi Persyaratan Kerja Aman Tambahan dari <i>Permit Issuer</i>
                <span style="font-weight: normal;">(jika ada)</span>
            </th>
            <th style="width: 10%; text-align: center; background-color: black; color: white;">(O)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2" style="height: 50px;">
                {{ $rekomendasi }}
            </td>
            <td style="text-align: center;">
                {!! $status === 'ya' ? $check : '' !!} Ya
            </td>
        </tr>
    </tbody>
</table>
{{-- 5. Permohonan Izin Kerja --}}
@php
    $name = $permit->permit_requestor_name ?? '-';
    $sign = $permit->permit_requestor_sign ?? null;
    $date = $permit->permit_requestor_date ?? '-';
    $time = $permit->permit_requestor_time ?? '-';
@endphp

<table class="table">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold;">
                5. Permohonan Izin Kerja
            </th>
        </tr>
        <tr>
            <th colspan="4"><i>Permit Requestor:</i></th>
        </tr>
        <tr>
            <td colspan="4">
                Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau
                rekomendasi persyaratan kerja aman tambahan dari <i>Permit Issuer</i> telah dipenuhi
                untuk dapat melakukan pekerjaan ini.
            </td>
        </tr>
        <tr>
            <th style="width: 25%;">Nama:</th>
            <th style="width: 25%;">Tanda tangan:</th>
            <th style="width: 25%;">Tanggal:</th>
            <th style="width: 25%;">Jam:</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $name }}</td>
            <td>
                @if($sign)
                    <img src="{{ public_path($sign) }}" alt="TTD" style="height: 50px;">
                @endif
            </td>
            <td>{{ $date }}</td>
            <td>{{ $time }}</td>
        </tr>
    </tbody>
</table>
{{-- 6. Penerbitan Izin Kerja --}}
@php
    $name = $permit->permit_issuer_name ?? '-';
    $sign = $permit->permit_issuer_sign ?? null;
    $date = $permit->permit_issuer_date ?? '-';
    $time = $permit->permit_issuer_time ?? '-';
    $from_date = $permit->izin_berlaku_dari ?? '-';
    $from_time = $permit->izin_berlaku_jam_dari ?? '-';
    $to_date = $permit->izin_berlaku_sampai ?? '-';
    $to_time = $permit->izin_berlaku_jam_sampai ?? '-';
@endphp

<table class="table">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold;">
                6. Penerbitan Izin Kerja
            </th>
        </tr>
        <tr>
            <th colspan="4"><i>Permit Issuer:</i></th>
        </tr>
        <tr>
            <td colspan="4">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan atau
                rekomendasi persyaratan kerja aman tambahan dari <i>Permit Issuer</i> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
            </td>
        </tr>
        <tr>
            <th style="width: 25%;">Nama:</th>
            <th style="width: 25%;">Tanda tangan:</th>
            <th style="width: 25%;">Tanggal:</th>
            <th style="width: 25%;">Jam:</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $name }}</td>
            <td>
                @if($sign)
                    <img src="{{ public_path($sign) }}" alt="TTD" style="height: 50px;">
                @endif
            </td>
            <td>{{ $date }}</td>
            <td>{{ $time }}</td>
        </tr>
        <tr>
            <td colspan="4">
                <b>Izin kerja ini berlaku dari tanggal:</b> {{ $from_date }} <b>jam:</b> {{ $from_time }} &nbsp;&nbsp;&nbsp;&nbsp;
                <b>sampai tanggal</b> {{ $to_date }} <b>jam:</b> {{ $to_time }}
            </td>
        </tr>
    </tbody>
</table>
{{-- 7. Pengesahan Izin Kerja --}}
@php
    $name = $permit->permit_authorizer_name ?? '-';
    $sign = $permit->permit_authorizer_sign ?? null;
    $date = $permit->permit_authorizer_date ?? '-';
    $time = $permit->permit_authorizer_time ?? '-';
@endphp

<table class="table">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold;">
                7. Pengesahan Izin Kerja
            </th>
        </tr>
        <tr>
            <th colspan="4"><i>Permit Authorizer:</i></th>
        </tr>
        <tr>
            <td colspan="4">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan atau
                rekomendasi persyaratan kerja aman tambahan dari <i>Permit Issuer</i> telah dipenuhi untuk dapat melakukan pekerjaan ini,
                serta saya sudah menekankan apa saja <i>major hazards</i> dan pengendaliannya yang harus disosialisasikan oleh
                <i>Permit Receiver</i> kepada seluruh pekerja terkait.
            </td>
        </tr>
        <tr>
            <th style="width: 25%;">Nama:</th>
            <th style="width: 25%;">Tanda tangan:</th>
            <th style="width: 25%;">Tanggal:</th>
            <th style="width: 25%;">Jam:</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $name }}</td>
            <td>
                @if($sign)
                    <img src="{{ public_path($sign) }}" alt="TTD" style="height: 50px;">
                @endif
            </td>
            <td>{{ $date }}</td>
            <td>{{ $time }}</td>
        </tr>
    </tbody>
</table>
{{-- 8. Pelaksanaan Pekerjaan --}}
@php
    $name = $permit->permit_receiver_name ?? '-';
    $sign = $permit->permit_receiver_sign ?? null;
    $date = $permit->permit_receiver_date ?? '-';
    $time = $permit->permit_receiver_time ?? '-';
@endphp

<table class="table">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold;">
                8. Pelaksanaan Pekerjaan
            </th>
        </tr>
        <tr>
            <th colspan="4"><i>Permit Receiver:</i></th>
        </tr>
        <tr>
            <td colspan="4">
                Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau
                rekomendasi persyaratan kerja aman tambahan dari <i>Permit Issuer</i> telah dipenuhi untuk dapat
                melakukan pekerjaan ini serta saya sudah mensosialisasikan apa saja <i>major hazards</i> dan pengendaliannya
                dari pekerjaan ini kepada seluruh pekerja terkait.
            </td>
        </tr>
        <tr>
            <th style="width: 25%;">Nama:</th>
            <th style="width: 25%;">Tanda tangan:</th>
            <th style="width: 25%;">Tanggal:</th>
            <th style="width: 25%;">Jam:</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $name }}</td>
            <td>
                @if($sign)
                    <img src="{{ public_path($sign) }}" alt="TTD" style="height: 50px;">
                @endif
            </td>
            <td>{{ $date }}</td>
            <td>{{ $time }}</td>
        </tr>
    </tbody>
</table>
{{-- 9. Live Testing --}}
@php
    $items = [
        'Peralatan/permesinan yang akan dinyalakan kembali (<i>re-energize</i>) sudah diidentifikasi.',
        'Semua peralatan kerja sudah dipindahkan/diamankan dari peralatan / permesinan yang akan dinyalakan kembali.',
        'Semua orang yang dekat dengan area kerja sudah diinformasikan akan adanya peralatan/permesinan yang akan dinyalakan.',
        'Orang-orang yang tidak berkepentingan harus berada di luar area.',
        '<i>Machine guarding</i> pada peralatan/permesinan yang tidak mempengaruhi proses <i>live testing</i> sudah dipasang kembali.',
        'Semua <i>lock</i> dan <i>tag</i> sudah dilepaskan.',
        '<i>Standby person</i> telah ditunjuk untuk memastikan bahwa tidak ada orang berada di sekitar area dimana terdapat peralatan mesin tanpa <i>machine guarding</i>.',
        'Peralatan/permesinan sudah dinyalakan kembali.',
        '<i>Setelah live testing, isolasi & penguncian harus kembali dipasang apabila pekerjaan belum selesai.</i>',
    ];
    $checklist = json_decode($permit->live_testing_items ?? '[]', true);
    $name = $permit->live_testing_name ?? '-';
    $sign = $permit->live_testing_sign ?? null;
    $date = $permit->live_testing_date ?? '-';
    $time = $permit->live_testing_time ?? '-';
@endphp

<table class="table">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold;">
                9. Live Testing <span style="font-weight: normal;">(Jika ada dan diisi oleh <i>Isolation Officer</i>)</span>
            </th>
            <th style="width: 15%; background-color: black; color: white; text-align: center;">(O)</th>
        </tr>
        <tr>
            <td colspan="3" style="font-size: 10px;">
                Jika peralatan/permesinan harus dinyalakan kembali (<i>live testing</i>), hal-hal berikut harus dilengkapi untuk memastikan pekerjaan dan kondisi area aman.
                Petugas Isolasi HIL harus memberi paraf untuk setiap tahap apabila sudah dilengkapi.
            </td>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $i => $text)
        <tr>
            <td colspan="2">{!! $text !!}</td>
            <td style="text-align: center;">
                {!! isset($checklist[$i]) && $checklist[$i] == 'ya' ? '☑ Ya' : '' !!}
            </td>
        </tr>
        @endforeach
        <tr>
            <th style="width: 25%;">Nama:</th>
            <th style="width: 25%;">Tanda tangan:</th>
            <th style="width: 25%;">Tanggal / Jam:</th>
        </tr>
        <tr>
            <td>{{ $name }}</td>
            <td>
                @if($sign)
                    <img src="{{ public_path($sign) }}" alt="TTD" style="height: 50px;">
                @endif
            </td>
            <td>{{ $date }} / {{ $time }}</td>
        </tr>
    </tbody>
</table>
{{-- 10. Penutupan Izin Kerja --}}
@php
    $lock = $closure?->lock_tag_removed ? '☑ Ya' : '';
    $tools = $closure?->equipment_cleaned ? '☑ Ya' : '';
    $guarding = $closure?->guarding_restored ? '☑ Ya' : '';
    $date = $closure?->closed_date ?? '-';
    $time = $closure?->closed_time ?? '-';
    $req_name = $closure?->requestor_name ?? '-';
    $req_sign = $closure?->requestor_sign ?? null;
    $iss_name = $closure?->issuer_name ?? '-';
    $iss_sign = $closure?->issuer_sign ?? null;
@endphp

<table class="table">
    <thead>
        <tr>
            <th colspan="3" style="background-color: black; color: white; font-weight: bold;">
                10. Penutupan Izin Kerja
            </th>
            <th style="width: 10%; background-color: black; color: white; text-align: center;">(O)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="width: 20%; font-style: italic;"><b>Lock & Tag</b></td>
            <td colspan="2">Semua <i>lock & tag</i> sudah dilepas</td>
            <td style="text-align: center;">{{ $lock }}</td>
        </tr>
        <tr>
            <td style="font-style: italic;"><b>Sampah & Peralatan Kerja</b></td>
            <td colspan="2">Semua sampah sudah dibersihkan dan peralatan kerja sudah diamankan</td>
            <td style="text-align: center;">{{ $tools }}</td>
        </tr>
        <tr>
            <td style="font-style: italic;"><b>Machine Guarding</b></td>
            <td colspan="2">Semua <i>machine guarding</i> sudah dipasang kembali</td>
            <td style="text-align: center;">{{ $guarding }}</td>
        </tr>

        {{-- Tanda Tangan --}}
        <tr>
            <th style="width: 25%;">Tanggal:</th>
            <th style="width: 25%;">Jam:</th>
            <th colspan="2" style="text-align: center;">Tanda Tangan</th>
        </tr>
        <tr>
            <td>{{ $date }}</td>
            <td>{{ $time }}</td>
            <td style="text-align: center;">
                {{ $req_name }}<br>
                @if($req_sign)
                    <img src="{{ public_path($req_sign) }}" style="height: 50px;" alt="TTD">
                @endif
                <div><i>Permit Requestor</i></div>
            </td>
            <td style="text-align: center;">
                {{ $iss_name }}<br>
                @if($iss_sign)
                    <img src="{{ public_path($iss_sign) }}" style="height: 50px;" alt="TTD">
                @endif
                <div><i>Permit Issuer</i></div>
            </td>
        </tr>
    </tbody>
</table>

</body>
</html>
