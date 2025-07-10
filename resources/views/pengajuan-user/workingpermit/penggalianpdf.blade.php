<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Izin Kerja Penggalian</title>
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
            <h2 style="margin: 0;">IZIN KERJA PENGGALIAN</h2>
        </td>
        <td style="border: 1px solid black; width: 25%;">
            <strong>Nomor:</strong> <span style="color: gray;">Jika ada</span>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="border: 1px solid black; font-size: 12px; padding: 5px; text-align: justify;">
            <p class="text-sm mt-2 text-gray-600 leading-snug">
        Izin kerja ini diberikan untuk semua pekerjaan penggalian dengan kedalaman ≥ 300 mm, izin ini tidak berlaku untuk penggalian di area tambang aktif.
        Pekerjaan tidak bisa dimulai hingga izin kerja diverifikasi oleh <em>Permit Verificator</em>, diterbitkan oleh <em>Permit Issuer</em>, disahkan oleh 
        <em>Permit Authorizer</em>, dan <em>major hazards & control</em> disosialisasikan oleh <em>Permit Receiver</em>.
    </p>
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

<!-- Bagian 2: Gambar/Denah Fasilitas Bawah Tanah -->
<table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; text-align: left; padding: 5px;">
                2. Gambar/Denah Fasilitas Bawah Tanah yang Diperlukan
            </th>
        </tr>
    </thead>
    <tbody>
        @php
            $denah = is_array($permit->denah) ? $permit->denah : json_decode($permit->denah, true) ?? [];

            $denahItems = [
                'kabel_listrik'     => 'Jalur kabel listrik',
                'optik_telpon'      => 'Jalur kabel optik/telpon',
                'pipa_gas'          => 'Jalur pipa gas',
                'pipa_proses'       => 'Jalur pipa proses',
                'cable_tunnel'      => 'Jalur cable tunnel',
                'pipa_hydrant'      => 'Jalur pipa air hydrant',
                'pipa_utilitas'     => 'Jalur pipa air utilitas',
                'kabel_instrumen'   => 'Jalur kabel instrumentasi',
                'selokan_septic'    => 'Jalur selokan dan septic tank',
                'lainnya'           => 'Jalur lainnya ……………………',
            ];

            $chunks = array_chunk($denahItems, ceil(count($denahItems) / 2), true);
        @endphp

        @for ($i = 0; $i < count($chunks[0]); $i++)
            <tr>
                @foreach ([0, 1] as $col)
                    @php
                        $key = array_keys($chunks[$col])[$i] ?? null;
                        $label = $key ? $chunks[$col][$key] : '';
                    @endphp
                    @if ($key)
                        <td style="border: 1px solid #000; padding: 5px;">
                            {!! $label !!}: {{ in_array($key, $denah) ? '✓' : '' }}
                        </td>
                    @else
                        <td style="border: 1px solid #000; padding: 5px;"></td>
                    @endif
                @endforeach
            </tr>
        @endfor

        <tr>
            <td colspan="2" style="border: 1px solid #000; padding: 5px;">
                <b>Status denah:</b> {{ $permit->denah_status === 'ya' ? '✓ Ya' : 'N/A' }}<br>

                @if($permit->denah_status === 'ya' && $permit->file_denah)
                    <b>Lampiran:</b> Gambar/denah terlampir
                @endif
            </td>
        </tr>

        @php
            $denahPath = storage_path('app/public/' . str_replace('storage/', '', $permit->file_denah));
        @endphp

        @if($permit->denah_status === 'ya' && $permit->file_denah && file_exists($denahPath))
            <tr>
                <td colspan="2" style="border: 1px solid #000; padding: 5px; text-align: center;">
                    <img src="{{ $denahPath }}" style="max-width: 100%; max-height: 400px;" alt="Gambar Denah">
                </td>
            </tr>
        @endif
    </tbody>
</table>

<!-- Bagian 3: Persyaratan Kerja Aman -->
<h3 style="background-color: black; color: white; padding: 5px;">3. Persyaratan Kerja Aman</h3>
<table style="width: 100%; border-collapse: collapse; font-size: 12px;">
    <thead>
        <tr>
            <th style="border: 1px solid #000; padding: 5px;">Persyaratan</th>
            <th style="border: 1px solid #000; padding: 5px; width: 30px;">Ya</th>
            <th style="border: 1px solid #000; padding: 5px; width: 30px;">N/A</th>
        </tr>
    </thead>
    <tbody>
        @php
            $items = [
                'Area penggalian sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
                'Area penggalian sudah dibatasi dengan memasang barikade atau <em>safety line</em>.',
                'Area kerja sudah diamankan dari potensi jatuhan benda/material.',
                'Area kerja sudah diamankan dari potensi pekerja terpeleset/tersandung.',
                'Pekerja yang melakukan pekerjaan penggalian kompeten terhadap pekerjaan yang dilakukan.',
                '<em>Job Safety Analysis/Safe Working Procedure</em> sudah tersedia dan semua pengendalian bahayanya sudah dilakukan.',
                'Peralatan kerja yang akan digunakan sudah diperiksa dan dipastikan layak untuk digunakan.',
                'Alat pelindung diri yang akan dipakai sudah diperiksa dan dinyatakan layak untuk dipakai.',
                'Semua pekerja penggalian sudah menggunakan alat pelindung diri yang sesuai.',
                'Tersedia jalur masuk/keluar galian yang aman, tersedia jembatan yang aman jika diperlukan.',
                'Tersedia tangga naik/turun dengan penempatan dan kemiringan yang aman (75°) dan terikat dengan kuat pada bibir galian.',
                'Semua pekerja penggalian memahami teknik <em>three point contact</em> saat naik/turun tangga.',
                'Area yang akan digali aman/sudah diamankan dari fasilitas bawah tanah seperti jalur kabel listrik, kabel optik, kabel instrumentasi, kabel telepon, pipa gas, pipa air <em>utility</em>, pipa proses, pipa air <em>hydrant</em>, selokan, <em>septic tank</em>, <em>cable tunnel</em>.',
                'Melakukan metoda <em>cutback</em> 30° untuk tanah keras dan 46° untuk tanah lunak/dipadatan.',
                'Metoda penyanggaan (<em>shoring</em>) lebih dari 3 meter disetujui oleh professional engineer.',
                'Penumpukan tanah galian minimum 2 meter dari bibir galian dan kemiringan kurang dari 45°.',
                'Garis barikade dengan jarak 2 meter dari bibir galian dan lengkap dengan rambu peringatan?',
                'Genangan air pada lubang galian dibuang/dipompa keluar dari lubang galian.',
                'Dipasang exhaust fan pada lubang galian untuk sirkulasi udara yang baik.',
                'Diterapkan <em>Protective support system</em> untuk penggalian yang akan dilakukan seperti <em>shoring/shielding/benching</em>.',
                'Dilakukan pengukuran kadar gas pada lubang galian (hasil pengukuran bisa dicatatkan pada lembar terpisah).',
                'Diperlukan isolasi dan penguncian sebelum penggalian dilakukan (ajukan Izin Kerja Umum).',
            ];
            $syarat = json_decode($permit->syarat_penggalian ?? '[]', true);
        @endphp

      @foreach ($items as $i => $item)
<tr>
    <td style="border: 1px solid #000; padding: 5px;">{!! $item !!}</td>
    <td style="border: 1px solid #000; text-align: center;">
        {{ ($syarat[$i] ?? '') === 'ya' ? '✓' : '' }}
    </td>
    <td style="border: 1px solid #000; text-align: center;">
        {{ ($syarat[$i] ?? '') === 'na' ? '✓' : '' }}
    </td>
</tr>
@endforeach

    </tbody>
</table>

<!-- Bagian 4: Rekomendasi Persyaratan Kerja Aman Tambahan -->
<table class="table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold; padding: 5px;">
                4. Rekomendasi Persyaratan Kerja Aman Tambahan dari <em>Permit Verificator/Permit Issuer</em>
            </th>
            <th style="background-color: black; color: white; text-align: center; width: 80px;">Status</th>
        </tr>
    </thead>
    <tbody>
        <tr style="height: 80px;">
            <td colspan="2" style="border: 1px solid #000; padding: 5px;">
                {!! nl2br(e($permit->rekomendasi_tambahan ?? '-')) !!}
            </td>
            <td style="border: 1px solid #000; text-align: center;">
                {{ $permit->rekomendasi_status === 'ya' ? '✓ Ya' : 'N/A' }}
            </td>
        </tr>
    </tbody>
</table>

<!-- Bagian 5: Permohonan Izin Kerja -->
<table class="table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; padding: 5px;">
                5. Permohonan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr><td colspan="4" style="font-style: italic; font-weight: bold; padding: 5px;">Permit Requestor:</td></tr>
        <tr><td colspan="4" style="padding: 5px;">
            Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
            <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk dapat melakukan pekerjaan ini.
        </td></tr>
        <tr>
            <th style="border: 1px solid #000; padding: 5px;">Nama</th>
            <th style="border: 1px solid #000; padding: 5px;">Tanda Tangan</th>
            <th style="border: 1px solid #000; padding: 5px;">Tanggal</th>
            <th style="border: 1px solid #000; padding: 5px;">Jam</th>
        </tr>
        <tr style="height: 60px;">
            <td style="border: 1px solid #000; padding: 5px; text-align: center;">
                {{ $permit->permit_requestor_name ?? '-' }}
            </td>
            <td style="border: 1px solid #000; padding: 5px; text-align: center;">
                @if ($permit->signature_permit_requestor && file_exists(public_path($permit->signature_permit_requestor)))
                    <img src="{{ public_path($permit->signature_permit_requestor) }}" height="40" alt="TTD">
                @else
                    -
                @endif
            </td>
            <td style="border: 1px solid #000; padding: 5px; text-align: center;">
                {{ \Carbon\Carbon::parse($permit->permit_requestor_date)->format('d-m-Y') ?? '-' }}
            </td>
            <td style="border: 1px solid #000; padding: 5px; text-align: center;">
                {{ $permit->permit_requestor_time ?? '-' }}
            </td>
        </tr>
    </tbody>
</table>

<!-- Bagian 6: Verifikasi Izin Kerja -->
<table class="table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; padding: 5px;">
                6. Verifikasi Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="font-style: italic; font-weight: bold; padding: 5px;">
                Digging Permit Verificator:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="padding: 5px;">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman yang telah
                ditentukan dan/atau rekomendasi persyaratan kerja aman tambahan dari
                <em>Permit Verificator/Permit Issuer</em> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
            </td>
        </tr>
        <tr>
            <th style="border: 1px solid #000; text-align: center;">Nama</th>
            <th style="border: 1px solid #000; text-align: center;">Tanda Tangan</th>
            <th style="border: 1px solid #000; text-align: center;">Tanggal</th>
            <th style="border: 1px solid #000; text-align: center;">Jam</th>
        </tr>
        <tr style="height: 60px;">
            <td style="border: 1px solid #000; text-align: center;">{{ $permit->verificator_name ?? '-' }}</td>
            <td style="border: 1px solid #000; text-align: center;">
                @if ($permit->signature_verificator && file_exists(public_path($permit->signature_verificator)))
                    <img src="{{ public_path($permit->signature_verificator) }}" height="40" alt="TTD">
                @else
                    -
                @endif
            </td>
            <td style="border: 1px solid #000; text-align: center;">
                {{ \Carbon\Carbon::parse($permit->verificator_date)->format('d-m-Y') ?? '-' }}
            </td>
            <td style="border: 1px solid #000; text-align: center;">
                {{ $permit->verificator_time ?? '-' }}
            </td>
        </tr>
    </tbody>
</table>
<!-- Bagian 7: Penerbitan Izin Kerja -->
<table class="table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; padding: 5px;">
                7. Penerbitan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="font-style: italic; font-weight: bold; padding: 5px;">Permit Issuer:</td>
        </tr>
        <tr>
            <td colspan="4" style="padding: 5px;">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan/atau
                rekomendasi persyaratan kerja aman tambahan telah dipenuhi untuk pekerjaan ini dapat dilakukan.
            </td>
        </tr>
        <tr>
            <th style="border: 1px solid #000; text-align: center;">Nama</th>
            <th style="border: 1px solid #000; text-align: center;">Tanda Tangan</th>
            <th style="border: 1px solid #000; text-align: center;">Tanggal</th>
            <th style="border: 1px solid #000; text-align: center;">Jam</th>
        </tr>
        <tr style="height: 60px;">
            <td style="border: 1px solid #000; text-align: center;">{{ $permit->permit_issuer_name ?? '-' }}</td>
            <td style="border: 1px solid #000; text-align: center;">
                @if ($permit->signature_permit_issuer && file_exists(public_path($permit->signature_permit_issuer)))
                    <img src="{{ public_path($permit->signature_permit_issuer) }}" height="40" alt="TTD">
                @else
                    -
                @endif
            </td>
            <td style="border: 1px solid #000; text-align: center;">
                {{ \Carbon\Carbon::parse($permit->permit_issuer_date)->format('d-m-Y') ?? '-' }}
            </td>
            <td style="border: 1px solid #000; text-align: center;">
                {{ $permit->permit_issuer_time ?? '-' }}
            </td>
        </tr>
        <tr>
            <td colspan="4" style="padding: 5px;">
                <b>Izin kerja ini berlaku dari tanggal:</b>
                {{ $permit->izin_berlaku_dari ?? '-' }}
                <b>jam:</b> {{ $permit->izin_berlaku_jam_dari ?? '-' }} &nbsp;
                <b>sampai tanggal</b> {{ $permit->izin_berlaku_sampai ?? '-' }}
                <b>jam:</b> {{ $permit->izin_berlaku_jam_sampai ?? '-' }}
            </td>
        </tr>
    </tbody>
</table>

<!-- Bagian 8: Pengesahan Izin Kerja -->
<table class="table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; padding: 5px;">
                8. Pengesahan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="font-style: italic; font-weight: bold; padding: 5px;">Permit Authorizer:</td>
        </tr>
        <tr>
            <td colspan="4" style="padding: 5px;">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan/atau
                rekomendasi tambahan dari <em>Permit Issuer</em> telah dipenuhi, serta saya sudah menekankan apa saja <em>major hazards</em> dan pengendaliannya yang harus disosialisasikan oleh <em>Permit Receiver</em> kepada seluruh pekerja terkait.
            </td>
        </tr>
        <tr>
            <th style="border: 1px solid #000; text-align: center;">Nama</th>
            <th style="border: 1px solid #000; text-align: center;">Tanda Tangan</th>
            <th style="border: 1px solid #000; text-align: center;">Tanggal</th>
            <th style="border: 1px solid #000; text-align: center;">Jam</th>
        </tr>
        <tr style="height: 60px;">
            <td style="border: 1px solid #000; text-align: center;">{{ $permit->permit_authorizer_name ?? '-' }}</td>
            <td style="border: 1px solid #000; text-align: center;">
                @if ($permit->signature_permit_authorizer && file_exists(public_path($permit->signature_permit_authorizer)))
                    <img src="{{ public_path($permit->signature_permit_authorizer) }}" height="40" alt="TTD">
                @else
                    -
                @endif
            </td>
            <td style="border: 1px solid #000; text-align: center;">
                {{ \Carbon\Carbon::parse($permit->permit_authorizer_date)->format('d-m-Y') ?? '-' }}
            </td>
            <td style="border: 1px solid #000; text-align: center;">
                {{ $permit->permit_authorizer_time ?? '-' }}
            </td>
        </tr>
    </tbody>
</table>
<!-- Bagian 9: Pelaksanaan Pekerjaan -->
<table class="table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; padding: 5px;">
                9. Pelaksanaan Pekerjaan
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="font-style: italic; font-weight: bold; padding: 5px;">Permit Receiver:</td>
        </tr>
        <tr>
            <td colspan="4" style="padding: 5px;">
                Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan/atau rekomendasi tambahan dari Permit Issuer telah dipenuhi untuk pekerjaan ini, serta saya telah mensosialisasikan <em>major hazards</em> dan pengendaliannya kepada seluruh pekerja terkait.
            </td>
        </tr>
        <tr>
            <th style="border: 1px solid #000; text-align: center;">Nama</th>
            <th style="border: 1px solid #000; text-align: center;">Tanda Tangan</th>
            <th style="border: 1px solid #000; text-align: center;">Tanggal</th>
            <th style="border: 1px solid #000; text-align: center;">Jam</th>
        </tr>
        <tr style="height: 60px;">
            <td style="border: 1px solid #000; text-align: center;">{{ $permit->permit_receiver_name ?? '-' }}</td>
            <td style="border: 1px solid #000; text-align: center;">
                @if ($permit->signature_permit_receiver && file_exists(public_path($permit->signature_permit_receiver)))
                    <img src="{{ public_path($permit->signature_permit_receiver) }}" height="40" alt="TTD">
                @else
                    -
                @endif
            </td>
            <td style="border: 1px solid #000; text-align: center;">
                {{ \Carbon\Carbon::parse($permit->permit_receiver_date)->format('d-m-Y') ?? '-' }}
            </td>
            <td style="border: 1px solid #000; text-align: center;">
                {{ $permit->permit_receiver_time ?? '-' }}
            </td>
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