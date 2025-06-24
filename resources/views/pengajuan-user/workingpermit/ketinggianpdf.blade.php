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
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <tr>
        <td style="border: 1px solid black; width: 20%; text-align: center;">
            <img src="file://{{ public_path('images/logo-st.png') }}" alt="Logo Perusahaan" style="width: 40%; height: auto;">
        </td>
        <td style="border: 1px solid black; text-align: center;" colspan="2">
            <h2 style="margin: 0;">IZIN KERJA</h2>
            <h3 style="margin: 0;">BEKERJA DI KETINGGIAN</h3>
        </td>
        <td style="border: 1px solid black; width: 25%;">
            <strong>Nomor:</strong> <span style="color: gray;">Jika ada</span>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="border: 1px solid black; font-size: 12px; padding: 5px; text-align: justify;">
          Izin kerja ini diberikan untuk pekerjaan di ketinggian yang memiliki risiko terjatuh dari ketinggian ≥ 1.8 m, penggunaan man basket/man box dengan mobile crane/hoist winch atau yang sejenis untuk mengangkat orang serta penggunaan gondola. Pekerjaan tidak bisa dimulai hingga izin kerja di verifikasi oleh Permit Verificator, diterbitkan oleh Permit Issuer, disahkan oleh Permit Authorizer dan major hazards & control disosialisasikan oleh Permit Receiver.
    </tr>
</table>
<br>  

<!-- Bagian 1: Detail Pekerjaan -->
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold; text-align: center; padding: 5px;">
                1. Detail Pekerjaan
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; width: 50%; padding: 5px;">Lokasi pekerjaan:</td>
            <td style="border: 1px solid black; padding: 5px;">Tanggal:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; padding: 5px;">{{ $detail?->location }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ \Carbon\Carbon::parse($detail?->work_date)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid black; font-weight: bold; padding: 5px;">Uraian pekerjaan:</td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid black; padding: 5px;">{{ $detail?->job_description }}</td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid black; font-weight: bold; padding: 5px;">Peralatan/perlengkapan yang akan digunakan pada pekerjaan:</td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid black; padding: 5px;">{{ $detail?->equipment }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; padding: 5px;">Perkiraan jumlah pekerja yang akan terlibat dalam pekerjaan ini:</td>
            <td style="border: 1px solid black; font-weight: bold; padding: 5px;">Nomor gawat darurat yang harus dihubungi saat darurat:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; padding: 5px;">{{ $detail?->worker_count }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ $detail?->emergency_contact }}</td>
        </tr>
    </tbody>
</table>
@php
    $pekerjaList = is_array($permit->nama_pekerja)
        ? $permit->nama_pekerja
        : json_decode($permit->nama_pekerja, true) ?? [];
@endphp

{{-- BAGIAN 2: Daftar Pekerja dan Sketsa Pekerjaan --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; margin-bottom: 0;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; text-align: left; padding: 5px;">
                2. Daftar Pekerja dan Sketsa Pekerjaan
                <span style="font-weight: normal; font-size: 12px;">(bisa dalam lampiran terpisah)</span>
            </th>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 5px;">Nama</th>
            <th style="border: 1px solid black; padding: 5px;">Paraf</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pekerjaList as $item)
            <tr>
                <td style="border: 1px solid black; padding: 5px;">
                    {{ $item['nama'] ?? '-' }}
                </td>
             <td style="border: 1px solid black; padding: 5px; text-align: center;">
    @if (!empty($item['signature']) && str_starts_with($item['signature'], 'data:image'))
        <img src="{{ $item['signature'] }}" style="height: 40px;">
    @else
        -
    @endif
</td>

            </tr>
        @endforeach
    </tbody>
</table>

{{-- SKETSA PEKERJAAN --}}
@if (!empty($permit->sketsa_pekerjaan))
  <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
    <tr>
        <td colspan="2" style="font-weight: bold; padding: 5px;">Sketsa Pekerjaan:</td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center; padding: 10px; border: 1px solid black;">
            <img src="{{ public_path($permit->sketsa_pekerjaan) }}"
                 alt="Sketsa Pekerjaan"
                 style="max-width: 400px; height: auto;">
        </td>
    </tr>
</table>

@endif


@php
    $items = [
        'Area kerja sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
        'Area kerja/area kerja dibawah sudah diamankan dari potensi jatuhan benda/material.',
        'Area kerja sudah diamankan dari potensi pekerja terpeleset/tersandung.',
        '<i>Job Safety Analysis/Safe Working Procedure</i> sudah tersedia dan semua pengendalian bahayanya sudah dilakukan.',
        'Tepi terbuka/bukaan di lantai sudah dipasang pagar pengaman sementara/ditutup yang mencegah pekerja terjatuh.',
        'Pekerja menggunakan full body harness yang difungsikan sebagai <i>fall restraint system</i>.',
        'Semua pekerja yang terlibat terlatih, memahami bahaya, risiko dan pengendaliannya untuk bekerja di ketinggian.',
        'Semua pekerja terlibat sudah dinyatakan fit untuk bekerja di ketinggian.',
        'Semua pekerja yang terlibat sudah menggunakan full body harness dan memahami cara penggunaanya dengan aman.',
        'Semua pekerja yang terlibat memahami dimana akan mengaitkan <i>hook</i> FBH nya saat bekerja di ketinggian.',
        'Semua pekerja terlibat memahami saat berpindah di ketinggian, <i>double hook</i> FBH nya dikaitkan secara bergantian.',
        'Semua pekerja terlibat memahami teknik <i>three point contact</i> saat naik/turun tangga.',
        '<i>Full body harness</i> yang akan dipakai sudah diperiksa dan dinyatakan layak dan aman untuk dipakai.',
        'Peralatan/perlengkapan kerja yang akan digunakan sudah diperiksa dan dipastikan layak dan aman untuk digunakan.',
        'Peralatan kerja genggam tangan yang berisiko jatuh diberi tali pengikat untuk dikaitkan dengan aman saat di ketinggian.',
        '<i>Man basket</i> sudah diperiksa, dipastikan layak dan aman untuk digunakan, pintu mengarah kedalam dan ada pengunci & <i>anchor point</i>.',
        'Gondola sudah diperiksa, dipastikan layak dan aman untuk digunakan, mempunyai surat izin alat dan operator kompeten.',
        '<i>Man basket</i>/gondola dilengkapi dengan <i>tag line</i> untuk mengendalikan pengangkatan.',
    ];

    // Pastikan decoding benar (jika array simpanan berupa string JSON)
    $checklist = is_array($permit->kerja_aman_ketinggian)
        ? $permit->kerja_aman_ketinggian
        : json_decode($permit->kerja_aman_ketinggian ?? '[]', true);
@endphp

<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; margin-top: 20px;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                3. Persyaratan Kerja Aman
            </th>
            <th style="background-color: black; color: white; text-align: center; width: 12%;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $index => $text)
            @php
                $status = $checklist[$index] ?? '';
              $mark = $status === 'ya' ? 'Ya' : ($status === 'na' ? 'N/A' : '');


            @endphp
            <tr>
                <td style="width: 5%; border: 1px solid black; text-align: center;">•</td>
                <td style="border: 1px solid black; padding: 5px;">{!! $text !!}</td>
                <td style="border: 1px solid black; text-align: center;">{{ $mark }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; margin-top: 20px;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; text-align: left; padding: 5px;">
                4. Rekomendasi Persyaratan Kerja Aman Tambahan dari <i>Permit Verificator/Permit Issuer</i>
                <span style="font-size: 11px;">(jika ada)</span>
            </th>
            <th style="background-color: black; color: white; text-align: center; width: 10%;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2" style="border: 1px solid black; padding: 10px; min-height: 80px;">
                {{ $permit->rekomendasi_tambahan ?? '' }}
            </td>
            <td style="border: 1px solid black; text-align: center;">
                {{ $permit->rekomendasi_ada === 'ya' ? '✓ Ya' : '✕ N/A' }}
            </td>
        </tr>
    </tbody>
</table>


<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; margin-top: 25px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; text-align: left; padding: 5px;">
                5. Permohonan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">
                Permit Requestor:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px;">
                Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk dapat melakukan pekerjaan ini.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Nama</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanda tangan</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanggal</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Jam</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; text-align: center;">{{ $permit->permit_requestor_name ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">
                @if ($permit->signature_permit_requestor)
                    <img src="{{ public_path($permit->signature_permit_requestor) }}" height="40">
                @endif
            </td>
            <td style="border: 1px solid black; text-align: center;">
                {{ \Carbon\Carbon::parse($permit->permit_requestor_date)->format('d-m-Y') ?? '' }}
            </td>
            <td style="border: 1px solid black; text-align: center;">
                {{ $permit->permit_requestor_time ?? '' }}
            </td>
        </tr>
    </tbody>
</table>

@php
    $authorizedWorkers = is_array($permit->authorized_workers)
        ? $permit->authorized_workers
        : json_decode($permit->authorized_workers ?? '[]', true);
@endphp

<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; margin-top: 25px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; padding: 5px; text-align: left;">
                6. Verifikasi Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">
                Working at Height Permit Verificator:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px;">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi tambahan sudah dipenuhi.
                <strong> Berikut nama-nama pekerja yang diizinkan:</strong>
            </td>
        </tr>

        @foreach ($authorizedWorkers as $name)
            <tr>
                <td colspan="4" style="border: 1px solid black; padding: 4px;">{{ $name }}</td>
            </tr>
        @endforeach

        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Nama</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanda tangan</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanggal</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Jam</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; text-align: center;">{{ $permit->verifikator_name ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">
                @if ($permit->signature_verifikator)
                    <img src="{{ public_path($permit->signature_verifikator) }}" height="40">
                @endif
            </td>
            <td style="border: 1px solid black; text-align: center;">
                {{ \Carbon\Carbon::parse($permit->verifikator_date)->format('d-m-Y') ?? '' }}
            </td>
            <td style="border: 1px solid black; text-align: center;">
                {{ $permit->verifikator_time ?? '' }}
            </td>
        </tr>
    </tbody>
</table>

<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; margin-top: 25px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; padding: 5px; text-align: left;">
                7. Penerbitan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">
                Permit Issuer:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px;">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman sudah terpenuhi.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Nama</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanda tangan</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanggal</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Jam</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; text-align: center;">{{ $permit->permit_issuer_name ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">
                @if ($permit->signature_permit_issuer)
                    <img src="{{ public_path($permit->signature_permit_issuer) }}" height="40">
                @endif
            </td>
            <td style="border: 1px solid black; text-align: center;">
                {{ \Carbon\Carbon::parse($permit->permit_issuer_date)->format('d-m-Y') ?? '' }}
            </td>
            <td style="border: 1px solid black; text-align: center;">
                {{ $permit->permit_issuer_time ?? '' }}
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 6px; font-size: 13px;">
                Izin kerja ini berlaku dari tanggal:
                {{ \Carbon\Carbon::parse($permit->izin_berlaku_dari)->format('d-m-Y') ?? '' }} jam:
                {{ $permit->izin_berlaku_jam_dari ?? '' }} sampai tanggal:
                {{ \Carbon\Carbon::parse($permit->izin_berlaku_sampai)->format('d-m-Y') ?? '' }} jam:
                {{ $permit->izin_berlaku_jam_sampai ?? '' }}
            </td>
        </tr>
    </tbody>
</table>

<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; margin-top: 25px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; padding: 5px; text-align: left;">
                8. Pengesahan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">
                Permit Authorizer:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px;">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah menekankan apa saja 
                <i>major hazards</i> dan pengendaliannya yang harus disosialisasikan oleh <i>Permit Receiver</i> kepada seluruh pekerja terkait.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; text-align: center;"><strong>Nama</strong></td>
            <td style="border: 1px solid black; text-align: center;"><strong>Tanda tangan</strong></td>
            <td style="border: 1px solid black; text-align: center;"><strong>Tanggal</strong></td>
            <td style="border: 1px solid black; text-align: center;"><strong>Jam</strong></td>
        </tr>
        <tr>
            <td style="border: 1px solid black; text-align: center;">{{ $permit->permit_authorizer_name ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">
                @if ($permit->signature_permit_authorizer)
                    <img src="{{ public_path($permit->signature_permit_authorizer) }}" height="40">
                @endif
            </td>
            <td style="border: 1px solid black; text-align: center;">
                {{ \Carbon\Carbon::parse($permit->permit_authorizer_date)->format('d-m-Y') ?? '' }}
            </td>
            <td style="border: 1px solid black; text-align: center;">
                {{ $permit->permit_authorizer_time ?? '' }}
            </td>
        </tr>
    </tbody>
</table>

<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; margin-top: 25px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; padding: 5px; text-align: left;">
                9. Pelaksanaan Pekerjaan
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">
                Permit Receiver:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px;">
                Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah mensosialisasikan apa saja 
                <i>major hazards</i> dan pengendaliannya dari pekerjaan ini kepada seluruh pekerja terkait.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; text-align: center;"><strong>Nama</strong></td>
            <td style="border: 1px solid black; text-align: center;"><strong>Tanda tangan</strong></td>
            <td style="border: 1px solid black; text-align: center;"><strong>Tanggal</strong></td>
            <td style="border: 1px solid black; text-align: center;"><strong>Jam</strong></td>
        </tr>
        <tr>
            <td style="border: 1px solid black; text-align: center;">{{ $permit->permit_receiver_name ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">
                @if ($permit->signature_permit_receiver)
                    <img src="{{ public_path($permit->signature_permit_receiver) }}" height="40">
                @endif
            </td>
            <td style="border: 1px solid black; text-align: center;">
                {{ \Carbon\Carbon::parse($permit->permit_receiver_date)->format('d-m-Y') ?? '' }}
            </td>
            <td style="border: 1px solid black; text-align: center;">
                {{ $permit->permit_receiver_time ?? '' }}
            </td>
        </tr>
    </tbody>
</table>

@php
    $lock = $closure?->lock_tag_removed ? '✓ Ya' : '✕ N/A';
    $tools = $closure?->equipment_cleaned ? '✓ Ya' : '✕ N/A';
    $guarding = $closure?->guarding_restored ? '✓ Ya' : '✕ N/A';
@endphp

<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 13px; margin-top: 25px;">
    <thead>
        <tr>
            <th colspan="3" style="background-color: black; color: white; text-align: left; padding: 5px;">
                10. Penutupan Izin Kerja
            </th>
            <th style="background-color: black; color: white; text-align: center;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">Lock & Tag</td>
            <td colspan="2" style="border: 1px solid black; padding: 5px;">Semua <i>lock & tag</i> sudah dilepas</td>
            <td style="border: 1px solid black; text-align: center;">{{ $lock }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">Sampah & Peralatan Kerja</td>
            <td colspan="2" style="border: 1px solid black; padding: 5px;">Semua sampah sudah dibersihkan dan peralatan kerja sudah diamankan</td>
            <td style="border: 1px solid black; text-align: center;">{{ $tools }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">Machine Guarding</td>
            <td colspan="2" style="border: 1px solid black; padding: 5px;">Semua <i>machine guarding</i> sudah dipasang kembali</td>
            <td style="border: 1px solid black; text-align: center;">{{ $guarding }}</td>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 5px;">Tanggal</th>
            <th style="border: 1px solid black; padding: 5px;">Jam</th>
            <th colspan="2" style="border: 1px solid black; text-align: center;">Tanda Tangan</th>
        </tr>
        <tr>
            <td style="border: 1px solid black; text-align: center;">
                {{ \Carbon\Carbon::parse($closure?->closed_date)->format('d-m-Y') ?? '' }}
            </td>
            <td style="border: 1px solid black; text-align: center;">
                {{ $closure?->closed_time ?? '' }}
            </td>
            <td style="border: 1px solid black; text-align: center;">
                @if ($closure?->requestor_sign)
                    <img src="{{ public_path($closure->requestor_sign) }}" height="40"><br><i>Permit Requestor</i>
                @endif
            </td>
            <td style="border: 1px solid black; text-align: center;">
                @if ($closure?->issuer_sign)
                    <img src="{{ public_path($closure->issuer_sign) }}" height="40"><br><i>Permit Issuer</i>
                @endif
            </td>
        </tr>
    </tbody>
</table>


</body>
</html>