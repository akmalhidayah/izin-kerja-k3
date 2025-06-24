<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Izin Bekerja Di Air</title>
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
 {{-- HEADER IZIN KERJA PERAIRAN --}}
 <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <tr>
        <td style="border: 1px solid black; width: 20%; text-align: center;">
            <img src="file://{{ public_path('images/logo-st.png') }}" alt="Logo Perusahaan" style="width: 40%; height: auto;">
        </td>
        <td style="border: 1px solid black; text-align: center;" colspan="2">
            <h2 style="margin: 0;">IZIN BEKERJA DI PERAIRAN</h2>
        </td>
        <td style="border: 1px solid black; width: 25%;">
            <strong>Nomor:</strong> <span style="color: gray;">Jika ada</span>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="border: 1px solid black; font-size: 12px; padding: 5px; text-align: justify;">
           <p style="text-align: justify; font-size: 11px; margin-top: 5px;">
    Izin kerja bekerja di air harus diterbitkan untuk semua pekerjaan pada/dekat/di air yang berpotensi menyebabkan tenggelam.
    Izin ini tidak berlaku pada pekerjaan yang sudah menjadi rutinitas misalnya menggunakan perahu/boat.
    Pekerjaan tidak bisa dimulai hingga izin kerja di verifikasi oleh <i>Permit Verificator</i>,
    diterbitkan oleh <i>Permit Issuer</i>, disahkan oleh <i>Permit Authorizer</i> dan <i>major hazards & control</i>
    disosialisasikan oleh <i>Permit Receiver</i>.
</p>
        </td>
    </tr>
</table>
<br>
<!-- Bagian 1: Detail Pekerjaan -->
<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; padding: 5px;">1. Detail Pekerjaan</th>
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
{{-- 2. Daftar Pekerja dan Sketsa Pekerjaan --}}
<table class="table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold;">
                2. Daftar Pekerja dan Sketsa Pekerjaan <span style="font-weight: normal;">(bisa dalam lampiran terpisah)</span>
            </th>
        </tr>
        <tr>
            <th style="width: 50%;">Nama</th>
            <th style="width: 50%;">Paraf</th>
        </tr>
    </thead>
    <tbody>
        @php
            $pekerja = json_decode($permit->daftar_pekerja ?? '[]', true);
        @endphp

        @forelse ($pekerja as $item)
        <tr>
            <td style="border: 1px solid #000; padding: 5px;">{{ $item['nama'] ?? '-' }}</td>
<td style="border: 1px solid #000; padding: 5px; text-align: center;">
    @if (!empty($item['paraf']) && str_starts_with($item['paraf'], 'data:image'))
        <img src="{{ $item['paraf'] }}" style="height: 40px;">
    @else
        -
    @endif
</td>

        </tr>
        @empty
        <tr>
            <td style="border: 1px solid #000; padding: 5px; height: 30px;"></td>
            <td style="border: 1px solid #000; padding: 5px;"></td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- Upload Sketsa Pekerjaan --}}
<div style="margin-top: 10px; border: 1px solid #000; padding: 5px;">
    <strong>Upload Sketsa Pekerjaan (jika diperlukan):</strong><br>
    @if($permit->sketsa_pekerjaan)
        <p style="margin-top: 10px;">Lampiran: {{ basename($permit->sketsa_pekerjaan) }}</p>
        <img src="{{ public_path($permit->sketsa_pekerjaan) }}" alt="Sketsa Pekerjaan" style="max-width: 100%; max-height: 300px; margin-top: 5px;">
    @else
        <p style="margin-top: 5px;">Belum ada sketsa pekerjaan yang dilampirkan.</p>
    @endif
    <small><em>* Lampirkan gambar sketsa bila diperlukan</em></small>
</div>

{{-- 3. Persyaratan Kerja Aman --}}
<table class="table">
    <thead>
        <tr>
            <th style="width: 85%; background-color: black; color: white; font-weight: bold;">
                3. Persyaratan Kerja Aman
            </th>
            <th style="width: 15%; background-color: black; color: white; text-align: center;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $items = [
                'Area kerja sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
                'Area tepi lantai kerja terbuka yang bisa sebabkan pekerja tenggelam di air sudah diamankan memasang pagar sementara/barikade yang mampu menahan beban pekerja, jika tidak memungkinkan minimum dipasang safety line/marka dengan rambu peringatan yang mengingatkan akan risiko tenggelam.',
                'Area kerja sudah diamankan dari potensi pekerja terpleset/tersandung.',
                '<i>Job Safety Analysis/Safe Working Procedure</i> sudah tersedia dan semua pengendalian bahayanya sudah dilakukan.',
                'Bukaan di lantai sudah dipasang pagar pengaman sementara/ditutup yang mencegah pekerja terjatuh ke air.',
                'Pekerja menggunakan full body harness yang difungsikan sebagai <i>fall restraint system</i> sehingga membatasi pekerja untuk mendekati area yang bisa menyebabkan tenggelam.',
                '<i>Life jacket</i> sudah diperiksa dan dinyatakan aman untuk dipakai.',
                'Semua pekerja yang berpotensi tenggelam bisa berenang dan sudah menggunakan <i>life jacket</i> yang sesuai.',
                'Semua pekerja yang terlibat terlatih, memahami bahaya, risiko dan pengendaliannya untuk bekerja di air.',
                'Semua pekerja penyelaman di air kompeten dan sudah dinyatakan fit untuk pekerjaan tersebut.',
                'Semua pekerja terlibat memahami teknik <i>three point contact</i> saat naik/turun tangga (contohnya tangga dermaga).',
                'Tim Rescue tersedia dan bisa segera datang melakukan pertolongan jika terjadi kondisi korban tenggelam.',
                'Peralatan/perlengkapan kerja yang akan digunakan sudah diperiksa dan dipastikan layak dan aman untuk digunakan.',
                'Peralatan pertolongan untuk menolong korban tenggelam tersedia seperti, <i>ringbuoy/lifebuoy</i>, <i>throwable device</i>, <i>basket stretcher</i> dll.',
                '<i>SCBA</i> yang akan digunakan sudah diperiksa, dan layak untuk dipakai dan digunakan oleh penyelam.',
                '<i>Boat/rubber boat</i> beserta seluruh kelengkapannya yang akan digunakan sudah diperiksa dan dinyatakan layak untuk digunakan.',
            ];

$raw = $permit->persyaratan_perairan ?? '[]';

    $firstDecode = json_decode($raw, true);
    $persyaratan = is_string($firstDecode)
        ? json_decode($firstDecode, true)
        : $firstDecode;
        @endphp
        @foreach($items as $index => $text)
        <tr>
            <td>{!! $text !!}</td>
            <td style="text-align: center;">
                @if(isset($persyaratan[$index]) && $persyaratan[$index] === 'ya')
                    ☑ Ya <span style="margin-left: 10px;">□ N/A</span>
                @else
                    □ Ya <span style="margin-left: 10px;">☑ N/A</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{-- 4. Rekomendasi Persyaratan Kerja Aman Tambahan --}}
<table class="table">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold;">
                4. Rekomendasi Persyaratan Kerja Aman Tambahan dari <i>Permit Verificator/Permit Issuer</i>
                <span style="font-weight: normal;">(jika ada)</span>
            </th>
            <th style="width: 10%; background-color: black; color: white; text-align: center;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2" style="height: 80px;">
                {{ $permit->rekomendasi_kerja_aman ?? '-' }}
            </td>
            <td style="text-align: center;">
                @if(($permit->rekomendasi_kerja_aman_check ?? '') === 'ya')
                    ☑ Ya <span style="margin-left: 10px;">□ N/A</span>
                @else
                    □ Ya <span style="margin-left: 10px;">☑ N/A</span>
                @endif
            </td>
        </tr>
    </tbody>
</table>
{{-- 5. Permohonan Izin Kerja --}}
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
                rekomendasi persyaratan kerja aman tambahan dari <i>Permit Verificator/Permit Issuer</i>
                telah dipenuhi untuk dapat melakukan pekerjaan ini.
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
            <td>{{ $permit->permit_requestor_name ?? '-' }}</td>
            <td>
                @if($permit->signature_permit_requestor)
                    <img src="{{ public_path($permit->signature_permit_requestor) }}" height="30">
                @else
                    -
                @endif
            </td>
            <td>{{ $permit->permit_requestor_date ? \Carbon\Carbon::parse($permit->permit_requestor_date)->format('d-m-Y') : '-' }}</td>
            <td>{{ $permit->permit_requestor_time ?? '-' }}</td>
        </tr>
    </tbody>
</table>
{{-- 6. Verifikasi Izin Kerja --}}
<table class="table">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold;">
                6. Verifikasi Izin Kerja
            </th>
        </tr>
        <tr>
            <th colspan="4"><i>Working at Water Permit Verificator:</i></th>
        </tr>
        <tr>
            <td colspan="4">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman
                yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
                <b>Berikut nama-nama pekerja yang diizinkan untuk bekerja di air:</b>
                <br>
                @foreach(json_decode($permit->verified_workers ?? '[]', true) as $worker)
                    - {{ $worker }} <br>
                @endforeach
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
            <td>{{ $permit->verificator_name ?? '-' }}</td>
            <td>
                @if($permit->signature_verificator)
                    <img src="{{ public_path($permit->signature_verificator) }}" alt="TTD" height="40">
                @else
                    -
                @endif
            </td>
            <td>{{ $permit->verificator_date ? \Carbon\Carbon::parse($permit->verificator_date)->format('d-m-Y') : '-' }}</td>
            <td>{{ $permit->verificator_time ?? '-' }}</td>
        </tr>
    </tbody>
</table>


{{-- 7. Penerbitan Izin Kerja --}}
<table class="table">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold;">
                7. Penerbitan Izin Kerja
            </th>
        </tr>
        <tr>
            <th colspan="4"><i>Permit Issuer:</i></th>
        </tr>
        <tr>
            <td colspan="4">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan
                dan atau rekomendasi persyaratan kerja aman tambahan telah dipenuhi untuk pekerjaan ini dapat dilakukan.
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
            <td>{{ $permit->permit_issuer_name ?? '-' }}</td>
            <td>
                @if($permit->signature_permit_issuer)
                    <img src="{{ public_path($permit->signature_permit_issuer) }}" alt="TTD" height="40">
                @endif
            </td>
            <td>{{ $permit->izin_berlaku_dari ? \Carbon\Carbon::parse($permit->izin_berlaku_dari)->format('d-m-Y') : '' }}</td>
            <td>{{ $permit->izin_berlaku_jam_dari ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="4">
                <b>Izin kerja ini berlaku dari tanggal:</b> {{ $permit->izin_berlaku_dari ? \Carbon\Carbon::parse($permit->izin_berlaku_dari)->format('d-m-Y') : '' }} &nbsp;&nbsp;
                <b>jam:</b> {{ $permit->izin_berlaku_jam_dari }} &nbsp;&nbsp;&nbsp;
                <b>sampai tanggal:</b> {{ $permit->izin_berlaku_sampai ? \Carbon\Carbon::parse($permit->izin_berlaku_sampai)->format('d-m-Y') : '' }} &nbsp;&nbsp;
                <b>jam:</b> {{ $permit->izin_berlaku_jam_sampai }}
            </td>
        </tr>
    </tbody>
</table>

{{-- 8. Pengesahan Izin Kerja --}}
<table class="table">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold;">
                8. Pengesahan Izin Kerja
            </th>
        </tr>
        <tr>
            <th colspan="4"><i>Permit Authorizer:</i></th>
        </tr>
        <tr>
            <td colspan="4">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan
                atau rekomendasi persyaratan kerja aman tambahan telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah
                menekankan apa saja <i>major hazards</i> dan pengendaliannya yang harus disosialisasikan oleh <i>Permit Receiver</i>
                kepada seluruh pekerja terkait.
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
            <td>{{ $permit->permit_authorizer_name ?? '-' }}</td>
            <td>
                @if($permit->signature_permit_authorizer)
                    <img src="{{ public_path($permit->signature_permit_authorizer) }}" alt="TTD" height="40">
                @endif
            </td>
            <td>{{ $permit->permit_authorizer_date ? \Carbon\Carbon::parse($permit->permit_authorizer_date)->format('d-m-Y') : '' }}</td>
            <td>{{ $permit->permit_authorizer_time ?? '' }}</td>
        </tr>
    </tbody>
</table>

{{-- 9. Pelaksanaan Pekerjaan --}}
<table class="table">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold;">
                9. Pelaksanaan Pekerjaan
            </th>
        </tr>
        <tr>
            <th colspan="4"><i>Permit Receiver:</i></th>
        </tr>
        <tr>
            <td colspan="4">
                Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja
                aman tambahan telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah mensosialisasikan apa saja
                <i>major hazards</i> dan pengendaliannya dari pekerjaan ini kepada seluruh pekerja terkait.
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
            <td>{{ $permit->permit_receiver_name ?? '-' }}</td>
            <td>
                @if($permit->signature_permit_receiver)
                    <img src="{{ public_path($permit->signature_permit_receiver) }}" alt="TTD" height="40">
                @endif
            </td>
            <td>{{ $permit->permit_receiver_date ? \Carbon\Carbon::parse($permit->permit_receiver_date)->format('d-m-Y') : '' }}</td>
            <td>{{ $permit->permit_receiver_time ?? '' }}</td>
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