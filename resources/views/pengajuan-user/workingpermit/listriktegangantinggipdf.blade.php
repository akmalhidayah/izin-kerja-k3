<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Izin Bekerja pada Listrik Tegangan Tinggi</title>
    <style>
        @page { size: A4; margin: 7mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font-family: "DejaVu Sans", sans-serif; font-size: 8px; line-height: 1.22; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #222; padding: 3px 4px; vertical-align: top; }
        .header td { border: 0; vertical-align: middle; }
        .logo { max-width: 90px; max-height: 46px; }
        .title { font-size: 16px; font-weight: bold; text-align: center; }
        .number { margin-top: 3px; font-size: 10px; font-weight: bold; text-align: center; }
        .section { margin-top: 4px; background: #000; color: #fff; font-size: 10px; font-weight: bold; padding: 4px 6px; }
        .label { font-weight: bold; }
        .center { text-align: center; }
        .small { font-size: 7px; }
        .value-box { min-height: 18px; }
        .page-break { page-break-after: always; }
        .signature { max-width: 105px; max-height: 42px; }
        .no-border { border: 0; }
        .instructions { min-height: 300px; white-space: pre-wrap; }
    </style>
</head>
<body>
@php
    $documentationItems = [
        'method_statement' => 'Metode kerja (JPT, method statement, dll.) sudah didiskusikan dan disetujui oleh Supervisor Kontraktor/Pekerja dan Permit Issuer.',
        'high_hazard_checklist' => 'High Hazard Checklist telah diperiksa untuk memastikan semua bahaya sudah diperiksa.',
        'supporting_documents' => 'Dokumen pendukung telah dilengkapi untuk diberikan kepada Supervisor Kontraktor/Pekerja (gambar rangkaian listrik, dll.).',
        'hazards_documented_in_lst' => 'Pekerjaan telah diperiksa ulang dan semua bahaya signifikan telah didokumentasikan di LST.',
    ];
    $safetyItems = [
        'electrical_worker_certificate' => 'Semua Petugas Listrik memiliki sertifikat keahlian yang masih berlaku dan sesuai.',
        'measurement_calibration_certificate' => 'Semua peralatan pengukuran memiliki sertifikat kalibrasi yang masih berlaku dan sesuai.',
        'fire_detection_protection' => 'Sistem perlindungan dan deteksi kebakaran tersedia dalam mode otomatis.',
        'portable_fire_extinguisher' => 'Alat pemadam kebakaran ringan tersedia, dalam kondisi baik, dan dapat digunakan.',
        'insulated_tools' => 'Peralatan dan perkakas kerja dilindungi bahan anti-statis atau bahan tidak menghantarkan listrik pada pegangannya.',
        'non_conductive_ppe' => 'APD dibuat dari bahan anti-statis atau bahan yang tidak menghantarkan listrik.',
        'non_conductive_ladder' => 'Tangga terbuat dari bahan bukan logam atau bahan yang tidak menghantarkan listrik.',
        'warning_signs' => 'Rambu bahaya yang sesuai dipasang pada tempat yang mudah terlihat.',
        'electrical_energy_isolation' => 'Kegiatan isolasi energi listrik perlu dilakukan (Sertifikat Kerja Isolasi Energi dilampirkan).',
        'gfci_elcb' => 'GFCI atau ELCB tersedia dan dalam kondisi bekerja (bukan MCB).',
        'nearby_workers_notified' => 'Pekerja lain di dekat lokasi telah diberi tahu dan memahami bahaya pekerjaan.',
    ];
    $issuerStatements = [
        'area_and_equipment_checked' => 'Saya telah memeriksa area kerja dan seluruh peralatan yang akan digunakan untuk bekerja di tegangan tinggi.',
        'hazard_control_checklist_reviewed' => 'Saya telah memeriksa ulang checklist bahaya dan kontrol untuk bekerja di tegangan tinggi.',
        'hazards_discussed_with_supervisor' => 'Saya telah berdiskusi dengan Supervisor mengenai seluruh bahaya dan kontrol yang tertulis pada izin kerja ini.',
        'high_risk_hazards_emphasized' => 'Saya telah menekankan bahaya berisiko tinggi kepada Supervisor Kontraktor/Pekerja untuk dicatat dalam LST.',
        'work_declared_safe' => 'Pekerjaan dinyatakan aman dengan pengendalian pada bagian 4 telah dilakukan dan dijaga.',
    ];
    $documentationValues = $permit->documentation_checks ?? [];
    $safetyValues = $permit->safety_requirements ?? [];
    $issuerValues = $permit->issuer_confirmations ?? [];
    $hazards = is_array($permit->additional_hazards ?? null) ? $permit->additional_hazards : [];
    while (count($hazards) < 5) {
        $hazards[] = ['hazard' => '', 'control' => '', 'pic' => ''];
    }
    $mark = fn ($actual, $expected) => $actual === $expected ? '[X]' : '[ ]';
    $date = fn ($value) => $value ? $value->format('d-m-Y') : '-';
    $signatureFile = function (?string $path): ?string {
        if (!$path || str_starts_with($path, 'data:image')) {
            return null;
        }
        $file = public_path($path);
        return file_exists($file) ? $file : null;
    };
@endphp

<table class="header">
    <tr>
        <td style="width: 20%;"><img src="file://{{ public_path('images/logo-sig.png') }}" class="logo" alt="SIG"></td>
        <td>
            <div class="title">IZIN BEKERJA PADA LISTRIK TEGANGAN TINGGI</div>
            <div class="number">NOMOR: {{ $permit->permit_number ?: '-' }}</div>
        </td>
        <td style="width: 20%; text-align: right;"><img src="file://{{ public_path('images/logo-st.png') }}" class="logo" alt="Semen Tonasa"></td>
    </tr>
</table>

<div class="section">Permit Issuer harus melengkapi seluruh bagian pada form ini.</div>
<div class="section">1. Detail Pekerjaan</div>
<table>
    <tr><td><span class="label">Lokasi Pekerjaan:</span> {{ $permit->work_location ?: '-' }}</td><td style="width: 31%;"><span class="label">Tanggal:</span> {{ $date($permit->work_date) }}</td></tr>
    <tr><td colspan="2"><span class="label">Peralatan/Mesin yang Akan Dikerjakan:</span> {{ $permit->equipment_machine ?: '-' }}</td></tr>
    <tr><td><span class="label">Nama Permit Issuer Bekerja di Tegangan Tinggi:</span> {{ $permit->permit_issuer_name ?: '-' }}</td><td><span class="label">No. HP:</span> {{ $permit->permit_issuer_phone ?: '-' }}</td></tr>
    <tr><td colspan="2" class="value-box"><span class="label">Uraian Pekerjaan dan Ruang Lingkup:</span><br>{{ $permit->job_scope ?: '-' }}<br><span class="label">Perkiraan Jumlah Pekerja:</span> {{ $permit->worker_count ?? '-' }}</td></tr>
    <tr><td colspan="2" class="value-box"><span class="label">Daftar Peralatan yang Digunakan:</span><br>{{ $permit->tools_equipment ?: '-' }}</td></tr>
</table>

<div class="section">2. Dokumentasi</div>
<table>
    <tr>
        <td><span class="label">Apakah izin kerja lain diperlukan?</span><br>Jenis dan nomor izin: {{ $permit->other_permit_details ?: '-' }}</td>
        <td class="center" style="width: 18%;">{{ $mark($permit->other_permit_status, 'ya') }} Ya&nbsp;&nbsp;{{ $mark($permit->other_permit_status, 'na') }} N/A</td>
    </tr>
    @foreach ($documentationItems as $key => $label)
        <tr>
            <td>{{ $label }}</td>
            <td class="center">{{ $mark($documentationValues[$key] ?? null, 'ya') }} Ya&nbsp;&nbsp;{{ $mark($documentationValues[$key] ?? null, 'na') }} N/A</td>
        </tr>
    @endforeach
    <tr><td colspan="2"><span class="label">Dokumen pendukung:</span> {{ $permit->supporting_document ? 'Terlampir' : '-' }}</td></tr>
</table>

<div class="section">3. Persyaratan Kesehatan dan Keselamatan Kerja Listrik</div>
<table class="small">
    <thead><tr><th>Pemeriksaan Kondisi Umum Pekerjaan</th><th style="width: 15%;">Butuh?<br>Ya / Tidak</th><th style="width: 15%;">Dipenuhi?<br>Ya / Tidak</th></tr></thead>
    <tbody>
        @foreach ($safetyItems as $key => $label)
            <tr>
                <td>{{ $label }}</td>
                <td class="center">{{ $mark($safetyValues[$key]['needed'] ?? null, 'ya') }} Ya&nbsp;{{ $mark($safetyValues[$key]['needed'] ?? null, 'tidak') }} Tidak</td>
                <td class="center">{{ $mark($safetyValues[$key]['fulfilled'] ?? null, 'ya') }} Ya&nbsp;{{ $mark($safetyValues[$key]['fulfilled'] ?? null, 'tidak') }} Tidak</td>
            </tr>
        @endforeach
        <tr><td colspan="3"><span class="label">Sertifikat Kerja Isolasi Energi:</span> {{ $permit->energy_isolation_certificate ? 'Terlampir' : '-' }}</td></tr>
    </tbody>
</table>

<div class="page-break"></div>

<div class="section">4. Bahaya dan Kontrol Tambahan yang Teridentifikasi</div>
<table>
    <thead><tr><th>Bahaya</th><th>Kontrol</th><th style="width: 14%;">PIC</th></tr></thead>
    <tbody>
        @foreach ($hazards as $row)
            <tr style="height: 25px;"><td>{{ $row['hazard'] ?? '' }}</td><td>{{ $row['control'] ?? '' }}</td><td>{{ $row['pic'] ?? '' }}</td></tr>
        @endforeach
    </tbody>
</table>

<div class="section">5. Handover Perizinan (Permit Issuer &amp; Receiver yang Melengkapi)</div>
<table>
    <tr><td colspan="2" class="label">Permit Issuer</td></tr>
    @foreach ($issuerStatements as $key => $statement)
        <tr><td>{{ $statement }}</td><td class="center" style="width: 10%;">{{ $mark($issuerValues[$key] ?? null, 'ya') }} Ya</td></tr>
    @endforeach
</table>
<table>
    <tr>
        <td style="width: 27%;"><span class="label">Nama:</span><br>{{ $permit->handover_issuer_name ?: '-' }}</td>
        <td style="width: 27%;"><span class="label">Tanda Tangan:</span><br>@if ($signatureFile($permit->handover_issuer_signature))<img src="file://{{ $signatureFile($permit->handover_issuer_signature) }}" class="signature" alt="TTD Issuer">@else - @endif</td>
        <td><span class="label">Tanggal:</span><br>{{ $date($permit->handover_issuer_date) }}</td>
        <td><span class="label">Jam:</span><br>{{ $permit->handover_issuer_time ?: '-' }}</td>
    </tr>
</table>
<table>
    <tr><td colspan="2" class="label">Permit Receiver</td></tr>
    <tr><td>Saya telah membaca dan mengerti syarat-syarat pada izin kerja ini dan akan melaksanakan pengendalian bahaya yang tertulis di sini.</td><td class="center" style="width: 10%;">{{ $mark($permit->receiver_confirmation, 'ya') }} Ya</td></tr>
</table>
<table>
    <tr>
        <td style="width: 27%;"><span class="label">Nama:</span><br>{{ $permit->receiver_name ?: '-' }}</td>
        <td style="width: 27%;"><span class="label">Tanda Tangan:</span><br>@if ($signatureFile($permit->receiver_signature))<img src="file://{{ $signatureFile($permit->receiver_signature) }}" class="signature" alt="TTD Receiver">@else - @endif</td>
        <td><span class="label">Tanggal:</span><br>{{ $date($permit->receiver_date) }}</td>
        <td><span class="label">Jam:</span><br>{{ $permit->receiver_time ?: '-' }}</td>
    </tr>
</table>

<div class="section">6. Instruksi Tambahan Persyaratan K3 Lainnya</div>
<div class="instructions" style="border: 1px solid #222; padding: 6px;">{{ $permit->additional_k3_instructions ?: '' }}</div>
</body>
</html>
