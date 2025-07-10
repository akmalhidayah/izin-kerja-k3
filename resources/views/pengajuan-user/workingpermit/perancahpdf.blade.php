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
            <h3 style="margin: 0;">PEKERJAAN PERANCAH</h3>
        </td>
        <td style="border: 1px solid black; width: 25%;">
            <strong>Nomor:</strong> <span style="color: gray;">Jika ada</span>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="border: 1px solid black; font-size: 12px; padding: 5px; text-align: justify;">
           Izin kerja ini diberikan untuk semua pekerjaan pemasangan dan pembongkaran perancah. Pekerjaan tidak bisa dimulai hingga izin kerja di verifikasi oleh 
            Permit Verificator, diterbitkan oleh Permit Issuer, disahkan oleh Permit Authorizer dan major hazards & control disosialisasikan oleh Permit Receiver. 
        </td>
    </tr>
</table>
{{-- 1. Detail Pekerjaan --}}
<div class="section-title" style="background-color: black; color: white; padding: 5px; font-weight: bold;">
    1. Detail Pekerjaan
</div>

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

    @if (!empty($permit->sketsa_perancah) && file_exists(public_path($permit->sketsa_perancah)))
    <tr>
        <th colspan="2" style="text-align: left;">Sketsa Perancah:</th>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center; padding-top: 10px;">
            <img src="{{ public_path($permit->sketsa_perancah) }}" style="max-width: 100%; max-height: 400px;" alt="Sketsa Perancah">
        </td>
    </tr>
    @endif

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


@php
    $checklist = json_decode($permit?->persyaratan_perancah ?? '[]', true);
    $items = [
        'Area kerja sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi, area dipasang safety line/barikade.',
        '<i>Job Safety Analysis/Safe Working Procedure</i> sudah tersedia untuk jenis perancah yang akan dipasang/didirikan.',
        'Scaffolder memiliki sertifikasi sebagai Teknisi Perancah, memakai FBH double hook sesuai dan paham penggunaannya.',
        'Scaffolder yang memasang perancah gantung dinyatakan fit untuk bekerja di ketinggian, perancah gantung memerlukan Izin Kerja Bekerja di Ketinggian.',
        'Material perancah dan semua aksesorisnya dalam kondisi layak pakai.',
        'Perancah kompleks (misalnya perancah di permukaan yang tingginya sampai puluhan <i>lift</i>) telah di review dan disetujui oleh <i>Civil Engineer</i> untuk dipasang/didirikan.',
    ];
@endphp

<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                2. Persyaratan Kerja Aman untuk Pemasangan/Pendirian Perancah
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
                {{ isset($checklist[$i]) ? ($checklist[$i] === 'ya' ? 'Ya' : ($checklist[$i] === 'na' ? 'N/A' : 'Tidak')) : 'Tidak' }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>


<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                3. Permohonan Izin Kerja untuk Pemasangan/Pendirian Perancah
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">Permit Requestor:</td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px; text-align: justify;">
                Saya menyatakan bahwa semua persyaratan kerja aman untuk pemasangan/pendirian perancah yang telah ditentukan telah dipenuhi untuk dapat memulai memasang/mendirikan perancah ini.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Nama:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanda tangan:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanggal:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black;">{{ $permit?->permit_requestor_name ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">
                @if ($permit?->signature_permit_requestor_perancah)
                    <img src="{{ public_path($permit->signature_permit_requestor_perancah) }}" height="50">
                @endif
            </td>
            <td style="border: 1px solid black;">{{ $permit?->permit_requestor_date ?? '' }}</td>
            <td style="border: 1px solid black;">{{ $permit?->permit_requestor_time ?? '' }}</td>
        </tr>
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                4. Verifikasi Izin Kerja untuk Pemasangan/Pendirian Perancah
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">
                Scaffolding Permit Verificator:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px; text-align: justify;">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman untuk pemasangan/pendirian perancah yang telah ditentukan telah dipenuhi untuk dapat memulai memasang/mendirikan perancah ini.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Nama:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanda tangan:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanggal:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black;">{{ $permit?->scaffolding_verificator_name ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">
                @if ($permit?->signature_scaffolding_verificator)
                    <img src="{{ public_path($permit->signature_scaffolding_verificator) }}" height="50" alt="TTD">
                @endif
            </td>
            <td style="border: 1px solid black;">{{ $permit?->scaffolding_verificator_date ?? '' }}</td>
            <td style="border: 1px solid black;">{{ $permit?->scaffolding_verificator_time ?? '' }}</td>
        </tr>
    </tbody>
</table>
{{-- Bagian 5 --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                5. Penerbitan Izin Kerja untuk Pemasangan/Pendirian Perancah
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">Permit Issuer:</td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px; text-align: justify;">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman untuk pemasangan/pendirian perancah yang telah ditentukan telah dipenuhi untuk dapat memulai memasang/mendirikan perancah ini.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Nama:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Tanda tangan:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Tanggal:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black;">{{ $permit?->permit_issuer_name }}</td>
            <td style="border: 1px solid black; text-align: center;">
                @if ($permit?->signature_permit_issuer)
                    <img src="{{ public_path($permit->signature_permit_issuer) }}" height="40">
                @endif
            </td>
            <td style="border: 1px solid black;">{{ $permit?->permit_issuer_date }}</td>
            <td style="border: 1px solid black;">{{ $permit?->permit_issuer_time }}</td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px;">
                Izin kerja ini berlaku dari tanggal: {{ $permit?->izin_berlaku_dari }} jam: {{ $permit?->izin_berlaku_jam_dari }} &nbsp;
                sampai tanggal {{ $permit?->izin_berlaku_sampai }} jam: {{ $permit?->izin_berlaku_jam_sampai }}
            </td>
        </tr>
    </tbody>
</table>

{{-- Bagian 6 --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px; margin-top: 10px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                6. Pengesahan Izin Kerja untuk Pemasangan/Pendirian Perancah
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">Permit Authorizer:</td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px; text-align: justify;">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman untuk pemasangan/pendirian perancah yang telah ditentukan telah dipenuhi untuk dapat memulai memasang/mendirikan perancah ini serta saya sudah menekankan apa saja <i>major hazards</i> dan pengendaliannya yang harus disosialisasikan oleh <i>Permit Receiver</i> kepada seluruh pekerja terkait.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Nama:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanda tangan:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanggal:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black;">{{ $permit?->permit_authorizer_name }}</td>
            <td style="border: 1px solid black; text-align: center;">
                @if ($permit?->signature_permit_authorizer)
                    <img src="{{ public_path($permit->signature_permit_authorizer) }}" height="40">
                @endif
            </td>
            <td style="border: 1px solid black;">{{ $permit?->permit_authorizer_date }}</td>
            <td style="border: 1px solid black;">{{ $permit?->permit_authorizer_time }}</td>
        </tr>
    </tbody>
</table>

{{-- Bagian 7 --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px; margin-top: 10px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                7. Pelaksanaan Pekerjaan untuk Pemasangan/Pendirian Perancah
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">Permit Receiver:</td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px; text-align: justify;">
                Saya menyatakan bahwa semua persyaratan kerja aman untuk pemasangan/pendirian perancah yang telah ditentukan telah dipenuhi untuk dapat memulai memasang/mendirikan perancah ini serta saya sudah mensosialisasikan apa saja <i>major hazards</i> dan pengendaliannya dari pekerjaan ini kepada seluruh pekerja terkait.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Nama:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanda tangan:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Tanggal:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black;">{{ $permit?->permit_receiver_name }}</td>
            <td style="border: 1px solid black; text-align: center;">
                @if ($permit?->signature_permit_receiver)
                    <img src="{{ public_path($permit->signature_permit_receiver) }}" height="40">
                @endif
            </td>
            <td style="border: 1px solid black;">{{ $permit?->permit_receiver_date }}</td>
            <td style="border: 1px solid black;">{{ $permit?->permit_receiver_time }}</td>
        </tr>
    </tbody>
</table>

{{-- Bagian 8 --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px; margin-top: 15px;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                8. Persyaratan Keselamatan Perancah yang Dipasang/Didirikan
            </th>
            <th style="background-color: black; color: white; text-align: center; width: 12%;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $items = [
                0 => 'Semua kaki-kaki perancah (<i>vertical standard</i>) dipasang tegak lurus...',
                1 => 'Semua ujung kaki-kaki perancah selain dipasang <i>base plate</i> juga dipasang <i>base pad</i>...',
                2 => 'Pemasangan <i>transom</i> dan <i>ledger</i> berada di dalam <i>vertical standard</i>...',
                3 => 'Posisi baut <i>clamp</i> pengikat menghadap ke atas...',
                4 => 'Jarak antara <i>vertical standard</i> secara menyamping...',
                5 => 'Semua <i>bracing</i> yang diperlukan sudah terpasang.',
                6 => 'Dipasang <i>outrigger</i> untuk menstabilkan perancah...',
                7 => '<i>Metal/wooden platform</i> yang dipasang kondisinya baik...',
                8 => 'Pagar pengaman dan toe <i>board</i> terpasang...',
                9 => 'Tersedia tangga naik/turun dengan penempatan dan kemiringan yang aman...',
                10 => 'Batas demarkasi/<i>safety line</i> telah dipasang...',
                11 => 'Kabel listrik dekat struktur perancah telah diamankan...',
                12 => 'Dipasang katrol yang aman untuk menaikkan/menurunkan barang.'
            ];
            $safety = json_decode($permit->persyaratan_keselamatan_perancah ?? '{}', true);
        @endphp

        @foreach ($items as $key => $text)
        <tr>
            <td style="width: 5%; border: 1px solid black; text-align: center;">•</td>
            <td style="border: 1px solid black; padding: 5px;">{!! $text !!}</td>
            <td style="border: 1px solid black; text-align: center;">
                {{ strtoupper($safety[$key] ?? '-') }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Bagian 9 --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; text-align: left; padding: 5px;">
                9. Rekomendasi Persyaratan Keselamatan Perancah Tambahan dari <i>Permit Verificator/Permit Issuer</i> <span style="font-weight: normal;">(jika ada)</span>
            </th>
            <th style="background-color: black; color: white; text-align: center; width: 8%;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2" style="border: 1px solid black; height: 60px;">{{ $permit->rekomendasi_keselamatan_perancah ?? '' }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ strtoupper($permit->rekomendasi_status ?? '-') }}</td>
        </tr>
    </tbody>
</table>
{{-- Bagian 10 --}}
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
      <tr>
    <td style="border: 1px solid black; text-align: center;">
        {{ $permit?->scaffolding_verificator_approval }}<br>
        @if ($permit?->signature_verificator_approval)
            <img src="{{ public_path($permit->signature_verificator_approval) }}" height="40">
        @endif
    </td>
    <td style="border: 1px solid black; text-align: center;">
        {{ $permit?->permit_issuer_approval }}<br>
        @if ($permit?->signature_issuer_approval)
            <img src="{{ public_path($permit->signature_issuer_approval) }}" height="40">
        @endif
    </td>
    <td colspan="2" style="border: 1px solid black; text-align: center;">
        {{ $permit?->permit_authorizer_approval }}<br>
        @if ($permit?->signature_authorizer_approval)
            <img src="{{ public_path($permit->signature_authorizer_approval) }}" height="40">
        @endif
    </td>
</tr>
<tr>
    <td colspan="2" style="border: 1px solid black; font-weight: bold;">
        Perancah ini berlaku dari tanggal:
        {{ $permit?->perancah_start_date ? \Carbon\Carbon::parse($permit->perancah_start_date)->format('d/m/Y') : '___/___/____' }}
        jam: {{ $permit?->perancah_start_time ?? '__:__' }}
    </td>
    <td colspan="2" style="border: 1px solid black; font-weight: bold;">
        sampai tanggal
        {{ $permit?->perancah_end_date ? \Carbon\Carbon::parse($permit->perancah_end_date)->format('d/m/Y') : '___/___/____' }}
        jam: {{ $permit?->perancah_end_time ?? '__:__' }}
    </td>
</tr>

    </tbody>
</table>

<table class="table">
    <thead>
        <tr>
            <th colspan="3" style="background-color: black; color: white; text-align: left; padding: 5px; font-weight: bold;">
                11. Penutupan Izin Kerja
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