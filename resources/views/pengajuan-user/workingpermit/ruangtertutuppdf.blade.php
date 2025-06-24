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
            <h3 style="margin: 0;">Bekerja di Ruang Tertutup/Terbatas</h3>
        </td>
        <td style="border: 1px solid black; width: 25%;">
            <strong>Nomor:</strong> <span style="color: gray;">Jika ada</span>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="border: 1px solid black; font-size: 12px; padding: 5px; text-align: justify;">
          Izin kerja bekerja di ruang tertutup/terbatas harus diterbitkan untuk semua pekerjaan yang dilakukan di ruang tertutup/terbatas baik rutin maupun non rutin. Pekerjaan tidak bisa dimulai hingga izin kerja diverifikasi oleh Permit Verificator, diterbitkan oleh Permit Issuer, disahkan oleh Permit Authorizer dan major hazards & control disosialisasikan oleh Permit Receiver.
</table>
<br>  


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


<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="6" style="background-color: black; color: white; text-align: left; padding: 5px;">
                2. Titik Isolasi dan Penguncian jika Diperlukan <span style="font-weight: normal;">(diisi oleh <i>Isolation Officer</i>, bisa melampirkan <i>checklist isolation points</i>)</span>
            </th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: left; background-color: #f0f0f0; border: 1px solid black; padding: 5px;">Isolasi energi listrik</th>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 5px;">Peralatan / Mesin</th>
            <th style="border: 1px solid black; padding: 5px;">Nomor peralatan/mesin</th>
            <th style="border: 1px solid black; padding: 5px;">Tempat Isolasi dan penguncian</th>
            <th style="border: 1px solid black; padding: 5px;">Locked, Tagged<br><span style="font-size: 11px;">(Ya / Tidak)</span></th>
            <th style="border: 1px solid black; padding: 5px;">Tested<br><span style="font-size: 11px;">(Ya / Tidak)</span></th>
            <th style="border: 1px solid black; padding: 5px;">Tanda Tangan <i>Isolation Officer</i></th>
        </tr>
    </thead>
    <tbody>
        @php
            $listrik = json_decode($permit->isolasi_listrik ?? '[]', true);
        @endphp
        @forelse ($listrik as $row)
            <tr>
                <td style="border: 1px solid black; padding: 5px;">{{ $row['peralatan'] ?? '' }}</td>
                <td style="border: 1px solid black; padding: 5px;">{{ $row['nomor'] ?? '' }}</td>
                <td style="border: 1px solid black; padding: 5px;">{{ $row['tempat'] ?? '' }}</td>
                <td style="border: 1px solid black; padding: 5px;">{{ $row['locked'] ?? '' }}</td>
                <td style="border: 1px solid black; padding: 5px;">{{ $row['tested'] ?? '' }}</td>
<td style="border: 1px solid black; padding: 5px;">
    @php $signature = $row['signature'] ?? null; @endphp
    @if ($signature && Str::startsWith($signature, 'data:image'))
        <img src="{{ $signature }}" alt="Signature" style="height: 40px;">
    @else
        {{ $signature ?? '-' }}
    @endif
</td>

            </tr>
        @empty
            <tr>
                <td colspan="6" style="border: 1px solid black; padding: 5px; text-align: center;">Tidak ada data isolasi listrik</td>
            </tr>
        @endforelse

        <tr>
            <td colspan="6" style="text-align: left; background-color: #f0f0f0; border: 1px solid black; padding: 5px;">
            <span style="font-weight: bold;">  Isolasi energi non listrik (<i>Thermal, chemical, radiation, potential, gravitational, mechanical</i> dan lain-lain)</span>
            </td>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 5px;">Peralatan / sumber energi</th>
            <th style="border: 1px solid black; padding: 5px;">Jenis<br><span style="font-size: 11px;">(Hose, Valve dll)</span></th>
            <th style="border: 1px solid black; padding: 5px;">Tempat isolasi dan penguncian</th>
            <th style="border: 1px solid black; padding: 5px;">Locked, Tagged<br><span style="font-size: 11px;">(Ya / Tidak)</span></th>
            <th style="border: 1px solid black; padding: 5px;">Tested<br><span style="font-size: 11px;">(Ya / Tidak)</span></th>
            <th style="border: 1px solid black; padding: 5px;">Tanda Tangan <i>Isolation Officer</i></th>
        </tr>
        @php
            $nonListrik = json_decode($permit->isolasi_non_listrik ?? '[]', true);
        @endphp
        @forelse ($nonListrik as $row)
            <tr>
                <td style="border: 1px solid black; padding: 5px;">{{ $row['peralatan'] ?? '' }}</td>
                <td style="border: 1px solid black; padding: 5px;">{{ $row['jenis'] ?? '' }}</td>
                <td style="border: 1px solid black; padding: 5px;">{{ $row['tempat'] ?? '' }}</td>
                <td style="border: 1px solid black; padding: 5px;">{{ $row['locked'] ?? '' }}</td>
                <td style="border: 1px solid black; padding: 5px;">{{ $row['tested'] ?? '' }}</td>
<td style="border: 1px solid black; padding: 5px;">
    @php $signature = $row['signature'] ?? null; @endphp
    @if ($signature && Str::startsWith($signature, 'data:image'))
        <img src="{{ $signature }}" alt="Signature" style="height: 40px;">
    @else
        {{ $signature ?? '-' }}
    @endif
</td>

            </tr>
        @empty
            <tr>
                <td colspan="6" style="border: 1px solid black; padding: 5px; text-align: center;">Tidak ada data isolasi non listrik</td>
            </tr>
        @endforelse
    </tbody>
</table>

<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="6" style="background-color: black; color: white; text-align: left; padding: 5px;">
                3. Pengukuran Berkala Kadar Gas di Udara <span style="font-weight: normal;">(diisi oleh <i>Permit Verificator</i>, bisa dalam lampiran terpisah)</span>
            </th>
        </tr>
       <tr>
    <th style="border: 1px solid black; font-style: italic; padding: 5px; text-align: center;">Item</th>
    <th style="border: 1px solid black; font-style: italic; padding: 5px; text-align: center;">NAB</th>
    <th style="border: 1px solid black; font-style: italic; padding: 5px; text-align: center;">Tanggal</th>
    <th style="border: 1px solid black; font-style: italic; padding: 5px; text-align: center;">Hasil</th>
    <th style="border: 1px solid black; font-style: italic; padding: 5px; text-align: center;">Jam</th>
    <th style="border: 1px solid black; font-style: italic; padding: 5px; text-align: center;">TTD</th>
</tr>

    </thead>
    <tbody>
        @php
            $pengukuranGas = json_decode($permit->pengukuran_gas ?? '{}', true);
        @endphp

        @foreach ($pengukuranGas as $item => $entries)
            @foreach ($entries as $i => $entry)
                <tr>
                    @if ($i === 0)
                        <td rowspan="{{ count($entries) }}" style="border: 1px solid black; text-align: center;">{{ explode('(', $item)[0] }}</td>
                        <td rowspan="{{ count($entries) }}" style="border: 1px solid black; text-align: center;">{{ explode('(', $item)[1] ?? '' }}</td>
                    @endif
                    <td style="border: 1px solid black; text-align: center;">{{ $entry['tgl'] ?? '' }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $entry['hasil'] ?? '' }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $entry['jam'] ?? '' }}</td>
                    <td style="border: 1px solid black; text-align: center;">
   @if (!empty($entry['sign']) && str_starts_with($entry['sign'], 'data:image'))
    <img src="{{ $entry['sign'] }}" style="height: 40px;">
@else
    -
@endif

</td>

                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
@php
    $items = [
        'Area kerja sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
        'Lubang/bukaan akses keluar masuk sudah tersedia, semua bukaan/ventilasi yang mampu dibuka sudah dibuka atau dipasang <i>exhaust fan</i> untuk sirkulasi udara, lubang/bukaan untuk evakuasi saat terjadi kondisi gawat darurat sudah ditentukan.',
        'Bukaan/akses gawat darurat tidak terhalangi oleh sesuatu yang bisa menghalangi keperluan evakuasi',
        'Dipasang <i>exhaust fan</i> untuk sirkulasi udara di dalam ruang tertutup/terbatas.',
        'Akumulasi material yang bisa menimpa/menimbun pekerja sudah dirontokkan/dijatuhkan.',
        'Area kerja sudah diamankan dari potensi jatuhan benda/material seperti memasang <i>protection roofing</i>.',
        'Semua peralatan dan APD sudah diperiksa dinyatakan layak, semua pekerja menggunakan APD yang sesuai.',
        '<i>Job Safety Analysis/Safe Working Procedure</i> sudah tersedia dan semua pengendalian bahayanya sudah dilakukan.',
        'Semua pekerja yang terlibat terlatih, memahami bahaya, risiko dan pengendaliannya untuk melakukan pekerjaan <i>confined space</i>.',
        'Semua pekerja terlibat sudah dinyatakan fit untuk bekerja di dalam ruang tertutup/terbatas.',
        'Semua peralatan dan permesinan sudah diisolasi, dikunci, diuji dan ditandai oleh <i>Isolation Officer</i>.',
        'Semua pekerja sudah memasang personal lock pada titik isolasi yang sudah ditentukan.',
        'Ada <i>standby person</i> yang mengetahui nomor gawat darurat dan memahami tanggung jawabnya seperti memantau kondisi area kerja, menjaga komunikasi dengan pekerja baik secara visual maupun verbal, mengawasi masuk/keluar orang dan lain-lain.',
        'Pencahayaan di dalam ruang tertutup/terbatas sudah mencukupi.',
        'Rencana penyelamatan dan peralatan pertolongan sudah tersedia untuk pekerjaan di dalam ruang terbatas/tertutup.',
        'Pekerjaan tertentu didalam silo pekerja menggunakan FBH yang terhubung dengan <i>anchorage point</i> .'
    ];

    $syaratValues = json_decode($permit->syarat_ruang_tertutup ?? '[]', true);
@endphp

{{-- BAGIAN 4 --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                4. Persyaratan Kerja Aman
            </th>
            <th style="background-color: black; color: white; text-align: center; width: 10%;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $i => $text)
        <tr>
            <td style="width: 5%; border: 1px solid black; text-align: center;">•</td>
            <td style="border: 1px solid black; padding: 5px;">{!! $text !!}</td>
            <td style="border: 1px solid black; text-align: center;">
                {{ $syaratValues[$i] ?? 'N/A' }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- BAGIAN 5 --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                5. Rekomendasi Persyaratan Kerja Aman Tambahan dari <i>Permit Verificator/Permit Issuer</i> <span style="font-weight: normal;">(jika ada)</span>
            </th>
            <th style="background-color: black; color: white; text-align: center; width: 8%;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2" style="border: 1px solid black; height: 80px; padding: 5px;">{{ $permit->rekomendasi_tambahan ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $permit->rekomendasi_status ?? 'N/A' }}</td>
        </tr>
    </tbody>
</table>

{{-- BAGIAN 6 --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                6. Permohonan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 4px;">
                Permit Requestor:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px;">
                Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk dapat melakukan pekerjaan ini.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Nama:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanda tangan:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanggal:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black;">{{ $permit->permit_requestor_name ?? '' }}</td>
            <td style="border: 1px solid black;">
                @if (!empty($permit->signature_permit_requestor))
                    <img src="{{ public_path($permit->signature_permit_requestor) }}" height="40">
                @endif
            </td>
            <td style="border: 1px solid black;">{{ $permit->permit_requestor_date ?? '' }}</td>
            <td style="border: 1px solid black;">{{ $permit->permit_requestor_time ?? '' }}</td>
        </tr>
    </tbody>
</table>
{{-- BAGIAN 7 --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                7. Verifikasi Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 4px;">
                Confined Space Permit Verificator:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px;">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
            </td>
        </tr>
        {{-- Header label --}}
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Nama:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanda tangan:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanggal:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Jam:</td>
        </tr>
        {{-- Data --}}
        <tr>
            <td style="border: 1px solid black; text-align: center;">{{ $permit->verificator_name ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">
                @if (!empty($permit->signature_verificator))
                 <img src="{{ public_path($permit->signature_verificator) }}" height="40">

                @endif
            </td>
            <td style="border: 1px solid black; text-align: center;">{{ $permit->verificator_date ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $permit->verificator_time ?? '' }}</td>
        </tr>
    </tbody>
</table>


{{-- BAGIAN 8 --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                8. Penerbitan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 4px;">
                Permit Issuer:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px;">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Nama:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanda tangan:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanggal:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black;">{{ $permit->permit_issuer_name ?? '' }}</td>
            <td style="border: 1px solid black;">
                @if (!empty($permit->signature_permit_issuer))
                  <img src="{{ public_path($permit->signature_permit_issuer) }}" height="40">

                @endif
            </td>
            <td style="border: 1px solid black;">{{ $permit->permit_issuer_date ?? '' }}</td>
            <td style="border: 1px solid black;">{{ $permit->permit_issuer_time ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px;">
                Izin kerja ini berlaku dari tanggal: {{ $permit->permit_start_date ?? ' / / ' }} jam: {{ $permit->permit_start_time ?? '' }} sampai tanggal {{ $permit->permit_end_date ?? ' / / ' }} jam: {{ $permit->permit_end_time ?? '' }}
            </td>
        </tr>
    </tbody>
</table>{{-- BAGIAN 9 --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                9. Pengesahan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 4px;">
                Permit Authorizer:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px;">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah menekankan apa saja <i>major hazards</i> dan pengendaliannya yang harus disosialisasikan oleh <i>Permit Receiver</i> kepada seluruh pekerja terkait.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Nama:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanda tangan:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanggal:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; text-align: center;">{{ $permit->permit_authorizer_name ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">
                @if (!empty($permit->signature_permit_authorizer))
                 <img src="{{ public_path($permit->signature_permit_authorizer) }}" height="40">

                @endif
            </td>
            <td style="border: 1px solid black; text-align: center;">{{ $permit->permit_authorizer_date ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $permit->permit_authorizer_time ?? '' }}</td>
        </tr>
    </tbody>
</table>

<br>

{{-- BAGIAN 10 --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                10. Pelaksanaan Pekerjaan
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 4px;">
                Permit Receiver:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px;">
                Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah mensosialisasikan apa saja <i>major hazards</i> dan pengendaliannya dari pekerjaan ini kepada seluruh pekerja terkait.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Nama:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanda tangan:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanggal:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; text-align: center;">{{ $permit->permit_receiver_name ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">
                @if (!empty($permit->signature_permit_receiver))
<img src="{{ public_path($permit->signature_permit_receiver) }}" height="40">

                @endif
            </td>
            <td style="border: 1px solid black; text-align: center;">{{ $permit->permit_receiver_date ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $permit->permit_receiver_time ?? '' }}</td>
        </tr>
    </tbody>
</table>

@php
    $listPekerja = json_decode($permit->pekerja_masuk_keluar ?? '[]', true);
@endphp
{{-- BAGIAN 11 --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 13px;">
    <thead>
        <tr>
            <th colspan="6" style="background-color: black; color: white; text-align: left; padding: 5px; font-weight: bold;">
                11. Daftar Pekerja Masuk/Keluar Ruang Tertutup/Terbatas
                <span style="font-weight: normal;">(tanda tangan di akhir shift, bisa dengan lampiran terpisah)</span>
            </th>
        </tr>
        <tr>
            <th style="border: 1px solid black;">Nama</th>
            <th style="border: 1px solid black;">Perusahaan</th>
            <th style="border: 1px solid black;">Tanggal</th>
            <th style="border: 1px solid black;">Waktu Masuk</th>
            <th style="border: 1px solid black;">Waktu Keluar</th>
            <th style="border: 1px solid black;">Sign</th>
        </tr>
    </thead>
    <tbody>
        @php
            $workers = json_decode($permit->pekerja_masuk_keluar ?? '[]');
        @endphp

        @foreach ($workers as $worker)
        <tr>
            <td style="border: 1px solid black; text-align: center;">{{ $worker->nama ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $worker->perusahaan ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $worker->tanggal ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $worker->masuk ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $worker->keluar ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">
                @if (!empty($worker->sign))
    <img src="{{ $worker->sign }}" height="40">
@endif

            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{-- BAGIAN 12 --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 13px;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; text-align: left; padding: 5px; font-weight: bold;">
                12. <i>Live Testing</i>
                <span style="font-weight: normal;">(Jika ada dan diisi oleh <i>Isolation Officer</i>)</span>
            </th>
            <th style="background-color: black; color: white; text-align: center;">(Centang)</th>
        </tr>
        <tr>
            <td colspan="3" style="border: 1px solid black; padding: 5px;">
                <span style="font-size: 12px;">
                    Jika peralatan/permesinan harus dinyalakan kembali (<i>live testing</i>), hal-hal berikut harus dilengkapi untuk memastikan pekerjaan dan kondisi area aman. <br>
                    <b>Petugas Isolasi HIL</b> harus memberi paraf untuk setiap tahap apabila sudah dilengkapi.
                </span>
            </td>
        </tr>
    </thead>
    <tbody>
        @php
            $liveTestingChecklist = json_decode($permit->live_testing_checklist ?? '[]', true);
            $liveTestingItems = [
                'Peralatan/permesinan yang akan dinyalakan kembali (<i>re-energize</i>) sudah diidentifikasi.',
                'Semua peralatan kerja sudah dipindahkan/diamankan dari peralatan / permesinan  yang akan dinyalakan kembali.',
                'Semua orang yang dekat dengan area kerja sudah diinformasikan akan adanya peralatan/permesinan yang akan dinyalakan.',
                'Orang-orang yang tidak berkepentingan harus  berada di luar area.',
                '<i>Machine guarding</i> pada peralatan/permesinan yang tidak mempengaruhi proses <i>live testing</i> sudah dipasang  kembali.',
                'Semua <i>lock and tag</i> sudah dilepaskan.',
                '<i>Standby person</i> telah ditunjuk untuk memastikan bahwa tidak ada  orang berada di sekitar area dimana terdapat peralatan mesin tanpa <i>machine guarding</i>.',
                'Peralatan/permesinan sudah dinyalakan kembali.',
                '<i>Setelah live testing, isolasi & penguncian harus kembali dipasang apabila pekerjaan belum selesai.</i>',
            ];
        @endphp

        @foreach ($liveTestingItems as $index => $item)
        <tr>
            <td style="width: 3%; border: 1px solid black; text-align: center;">*</td>
            <td style="border: 1px solid black; padding: 5px;">{!! $item !!}</td>
            <td style="border: 1px solid black; text-align: center;">
                {{ $liveTestingChecklist[$index] ?? '' }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<br>

<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 13px;">
    <thead>
        <tr>
            <th style="border: 1px solid black;">Nama:</th>
            <th style="border: 1px solid black;">Tanda tangan:</th>
            <th style="border: 1px solid black;">Tanggal:</th>
            <th style="border: 1px solid black;">Jam:</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid black; text-align: center;">{{ $permit->live_testing_name ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">
                @if (!empty($permit->live_testing_signature))
                  <img src="{{ public_path($permit->live_testing_signature) }}" height="40">

                @endif
            </td>
            <td style="border: 1px solid black; text-align: center;">{{ $permit->live_testing_date ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ $permit->live_testing_time ?? '' }}</td>
        </tr>
    </tbody>
</table>
<br>

{{-- 13. Penutupan Izin Kerja --}}
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
    $rfid = $closure?->jumlah_rfid ?? '-';
@endphp

<table class="table">
    <thead>
        <tr>
            <th colspan="3" style="background-color: black; color: white; text-align: left; padding: 5px; font-weight: bold;">
                13. Penutupan Izin Kerja
            </th>
            <th style="width: 10%; background-color: black; color: white; text-align: center;">(ya/na)</th>
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
            </td>
        </tr>
         <tr>
            <td colspan="2" style="font-weight: bold;">Jumlah RFID yang digunakan</td>
            <td colspan="2" style="text-align: center;">{{ $rfid }}</td>
        </tr>
    </tbody>
</table>
</body>
</html>