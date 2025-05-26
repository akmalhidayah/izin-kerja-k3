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
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <tr>
        <td style="border: 1px solid black; width: 20%; text-align: center;">
            <img src="file://{{ public_path('images/logo-st.png') }}" alt="Logo Perusahaan" style="width: 40%; height: auto;">
        </td>
        <td style="border: 1px solid black; text-align: center;" colspan="2">
            <h2 style="margin: 0;">IZIN KERJA</h2>
            <h3 style="margin: 0;">Pekerjaan dengan Material/Gas Panas ≥ 150 Celcius</h3>
        </td>
        <td style="border: 1px solid black; width: 25%;">
            <strong>Nomor:</strong> <span style="color: gray;">Jika ada</span>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="border: 1px solid black; font-size: 12px; padding: 5px; text-align: justify;">
            Izin kerja ini harus diterbitkan untuk semua pekerjaan dengan paparan material/gas panas ≥ 150°C Celcius seperti <i>cleaning bunker</i>, perbaikan pada <i>riser duct</i> saat kiln <i>running</i> dan lain-lain yang sejenis. Izin ini tidak berlaku untuk pekerjaan yang sudah menjadi rutinitas misalnya merojok <i>poke hole cyclone</i> dan-lain-lain yang sejenis.. Pekerjaan tidak bisa dimulai hingga izin kerja di verifikasi oleh <i>Permit Verificator</i>, diterbitkan oleh <i>Permit Issuer</i>, disahkan oleh <i>Permit Authorizer</i> dan <i>major hazards & control</i> disosialisasikan oleh <i>Permit Receiver</i>.
        </td>
    </tr>
</table>
<br>
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
<!-- Bagian 2: Daftar Pekerja dan Sketsa Pekerjaan -->
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; margin-top: 20px;">
    <thead>
        <tr>
            <th colspan="3" style="background-color: black; color: white; text-align: left; padding: 5px;">
                2. Daftar Pekerja dan Sketsa Pekerjaan <span style="font-weight: normal;">(bisa dalam lampiran terpisah)</span>
            </th>
        </tr>
        <tr>
            <th style="border: 1px solid black; text-align: center;">Nama</th>
            <th style="border: 1px solid black; text-align: center;">Paraf</th>
            <th style="border: 1px solid black; text-align: center;">Catatan (opsional)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $workers = json_decode($permit->daftar_pekerja ?? '[]', true);
        @endphp
        @for ($i = 0; $i < 5; $i++)
            <tr>
                <td style="border: 1px solid black;">
                    {{ $workers[$i]['nama'] ?? '' }}
                </td>
                <td style="border: 1px solid black; text-align: center;">
                    @if (!empty($workers[$i]['signature']))
                        <img src="{{ $workers[$i]['signature'] }}" alt="Signature" style="height: 40px;">
                    @endif
                </td>
                <td style="border: 1px solid black;"></td>
            </tr>
        @endfor
    </tbody>
</table>
<!-- Bagian 3: Checklist Persyaratan Kerja Aman -->
<!-- Bagian 3: Checklist Persyaratan Kerja Aman -->
@php
    $checklist = json_decode($permit->checklist_kerja_aman ?? '[]', true);
    $listSyarat = [
        'Area kerja sudah diperiksa...',
        'Area kerja sudah dibatasi...',
        'Area kerja sudah diamankan...',
        'Area kerja di bawah sudah diamankan...',
        'Tersedia jalur evakuasi...',
        'JSA sudah tersedia...',
        'Pekerja tidak sendiri...',
        'Pekerja terlatih dan paham risiko...',
        'Posisi aman dari semburan...',
        'Jarak aman dijaga...',
        'Kerja bergantian hindari heat stress...',
        'Tim rescue tersedia...',
        'Peralatan kerja layak pakai...',
        'APD tahan panas layak pakai...',
        'Pakaian pelindung sesuai...',
        'APD lengkap seluruh tubuh...',
        'Emergency shower tersedia...'
    ];
@endphp

<table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 11px;">
    <thead>
        <tr>
            <th style="background-color: black; color: white; text-align: left; padding: 5px;">3. Persyaratan Kerja Aman</th>
            <th style="background-color: black; color: white; text-align: center; padding: 5px; width: 25%;">(Beri Centang)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($listSyarat as $i => $label)
        <tr>
            <td style="border: 1px solid #000; padding: 5px;">{{ $label }}</td>
            <td style="border: 1px solid #000; text-align: center;">
                {!! isset($checklist[$i]) && $checklist[$i] === 'ya' ? '✔ Ya □ N/A' : (isset($checklist[$i]) && $checklist[$i] === 'na' ? '□ Ya ✔ N/A' : '□ Ya □ N/A') !!}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>


<!-- Bagian 4: Rekomendasi Tambahan -->
<table class="table" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold; padding: 4px;">
                4. Rekomendasi Persyaratan Kerja Aman Tambahan dari <i>Permit Verificator/Permit Issuer</i> <span style="font-weight: normal;">(jika ada)</span>
            </th>
            <th style="background-color: black; color: white; text-align: center; width: 10%;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2" style="height: 100px; border: 1px solid black; padding: 5px;">
                {{ $permit->rekomendasi_tambahan ?? '' }}
            </td>
            <td style="text-align: center; border: 1px solid black;">
                {{ $permit->rekomendasi_status === 'ya' ? '✓ Ya' : 'N/A' }}
            </td>
        </tr>
    </tbody>
</table>

<!-- Bagian 5: Permohonan Izin Kerja -->
<table class="table" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; padding: 4px;">5. Permohonan Izin Kerja</th>
        </tr>
    </thead>
    <tbody>
        <tr><td colspan="4" style="font-style: italic; font-weight: bold; padding: 4px;">Permit Requestor:</td></tr>
        <tr><td colspan="4" style="padding: 4px;">Saya menyatakan bahwa semua persyaratan kerja aman... telah dipenuhi untuk dapat melakukan pekerjaan ini.</td></tr>
        <tr>
            <th style="text-align: center;">Nama:</th>
            <th style="text-align: center;">Tanda tangan:</th>
            <th style="text-align: center;">Tanggal:</th>
            <th style="text-align: center;">Jam:</th>
        </tr>
        <tr style="height: 50px;">
            <td style="text-align: center;">{{ $permit->permit_requestor_name }}</td>
            <td style="text-align: center;">
                @if ($permit->permit_requestor_sign)
                    <img src="{{ public_path($permit->permit_requestor_sign) }}" alt="ttd" height="40">
                @endif
            </td>
            <td style="text-align: center;">{{ $permit->permit_requestor_date }}</td>
            <td style="text-align: center;">{{ $permit->permit_requestor_time }}</td>
        </tr>
    </tbody>
</table>

<!-- Bagian 6: Verifikasi Izin Kerja -->
<table class="table" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr><th colspan="4" style="background-color: black; color: white; font-weight: bold; padding: 4px;">6. Verifikasi Izin Kerja</th></tr>
    </thead>
    <tbody>
        <tr><td colspan="4" style="font-style: italic; font-weight: bold; padding: 4px;">Hot Material/Gasses Permit Verificator:</td></tr>
        <tr>
            <td colspan="4" style="padding: 4px;">Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi...</td>
        </tr>
        <tr>
            <th style="text-align: center;">Nama:</th>
            <th style="text-align: center;">Tanda tangan:</th>
            <th style="text-align: center;">Tanggal:</th>
            <th style="text-align: center;">Jam:</th>
        </tr>
        <tr style="height: 50px;">
            <td style="text-align: center;">{{ $permit->verificator_name }}</td>
            <td style="text-align: center;">
                @if ($permit->verificator_sign)
                    <img src="{{ public_path($permit->verificator_sign) }}" alt="ttd" height="40">
                @endif
            </td>
            <td style="text-align: center;">{{ $permit->verificator_date }}</td>
            <td style="text-align: center;">{{ $permit->verificator_time }}</td>
        </tr>
    </tbody>
</table>

<!-- Bagian 7: Penerbitan Izin Kerja -->
<table class="table" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr><th colspan="4" style="background-color: black; color: white; font-weight: bold; padding: 4px;">7. Penerbitan Izin Kerja</th></tr>
    </thead>
    <tbody>
        <tr><td colspan="4" style="font-style: italic; font-weight: bold; padding: 4px;">Permit Issuer:</td></tr>
        <tr>
            <td colspan="4" style="padding: 4px;">Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman...</td>
        </tr>
        <tr>
            <th style="text-align: center;">Nama:</th>
            <th style="text-align: center;">Tanda tangan:</th>
            <th style="text-align: center;">Tanggal:</th>
            <th style="text-align: center;">Jam:</th>
        </tr>
        <tr style="height: 50px;">
            <td style="text-align: center;">{{ $permit->permit_issuer_name }}</td>
            <td style="text-align: center;">
                @if ($permit->permit_issuer_sign)
                    <img src="{{ public_path($permit->permit_issuer_sign) }}" alt="ttd" height="40">
                @endif
            </td>
            <td style="text-align: center;">{{ $permit->permit_issuer_date }}</td>
            <td style="text-align: center;">{{ $permit->permit_issuer_time }}</td>
        </tr>
        <tr>
            <td colspan="4" style="padding: 4px;">
                <b>Izin kerja ini berlaku dari tanggal:</b>
                {{ $permit->izin_berlaku_dari ?? '...' }}
                <b>jam:</b> {{ $permit->izin_berlaku_jam_dari ?? '...' }} &nbsp;
                <b>sampai tanggal</b> {{ $permit->izin_berlaku_sampai ?? '...' }}
                <b>jam:</b> {{ $permit->izin_berlaku_jam_sampai ?? '...' }}
            </td>
        </tr>
    </tbody>
</table>
<!-- Bagian 8: Pengesahan Izin Kerja -->
<table class="table" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; padding: 4px;">
                8. Pengesahan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr><td colspan="4" style="font-style: italic; font-weight: bold; padding: 4px;">Permit Authorizer:</td></tr>
        <tr>
            <td colspan="4" style="padding: 4px;">
                Saya menyatakan bahwa saya telah memeriksa area kerja... disosialisasikan oleh <i>Permit Receiver</i> kepada seluruh pekerja terkait.
            </td>
        </tr>
        <tr>
            <th style="text-align: center;">Nama:</th>
            <th style="text-align: center;">Tanda tangan:</th>
            <th style="text-align: center;">Tanggal:</th>
            <th style="text-align: center;">Jam:</th>
        </tr>
        <tr style="height: 50px;">
            <td style="text-align: center;">{{ $permit->permit_authorizer_name }}</td>
            <td style="text-align: center;">
                @if ($permit->permit_authorizer_sign)
                    <img src="{{ public_path($permit->permit_authorizer_sign) }}" height="40" alt="TTD">
                @endif
            </td>
            <td style="text-align: center;">{{ $permit->permit_authorizer_date }}</td>
            <td style="text-align: center;">{{ $permit->permit_authorizer_time }}</td>
        </tr>
    </tbody>
</table>

<!-- Bagian 9: Pelaksanaan Pekerjaan -->
<table class="table" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; padding: 4px;">
                9. Pelaksanaan Pekerjaan
            </th>
        </tr>
    </thead>
    <tbody>
        <tr><td colspan="4" style="font-style: italic; font-weight: bold; padding: 4px;">Permit Receiver:</td></tr>
        <tr>
            <td colspan="4" style="padding: 4px;">
                Saya menyatakan bahwa semua persyaratan kerja aman... telah dipenuhi dan sudah disosialisasikan kepada seluruh pekerja terkait.
            </td>
        </tr>
        <tr>
            <th style="text-align: center;">Nama:</th>
            <th style="text-align: center;">Tanda tangan:</th>
            <th style="text-align: center;">Tanggal:</th>
            <th style="text-align: center;">Jam:</th>
        </tr>
        <tr style="height: 50px;">
            <td style="text-align: center;">{{ $permit->permit_receiver_name }}</td>
            <td style="text-align: center;">
                @if ($permit->permit_receiver_sign)
                    <img src="{{ public_path($permit->permit_receiver_sign) }}" height="40" alt="TTD">
                @endif
            </td>
            <td style="text-align: center;">{{ $permit->permit_receiver_date }}</td>
            <td style="text-align: center;">{{ $permit->permit_receiver_time }}</td>
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
            <th style="width: 10%; background-color: black; color: white; text-align: center;">(lingkari)</th>
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