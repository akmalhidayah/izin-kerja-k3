@php
    $steps = $jsa->langkah_kerja ?? [];

    if (is_string($steps)) {
        $decodedSteps = json_decode($steps, true);
        $steps = is_array($decodedSteps) ? $decodedSteps : [];
    }

    $steps = is_array($steps) ? $steps : [];

    $formatCell = static function ($value) {
        $text = trim((string) ($value ?? ''));
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/[ \t]{2,}/', ' ', $text);
        $text = preg_replace('/(?<!^)(?<!\n)\s+((?:\d+|[A-Za-z])\.)\s*/', "\n$1 ", $text);
        $text = preg_replace('/(^|\n)((?:\d+|[A-Za-z])\.)\s*/', "$1$2 ", $text);
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        return nl2br(e($text ?: '-'));
    };

    $signaturePath = static function ($path) {
        if (!$path) {
            return null;
        }

        if (is_file($path)) {
            return $path;
        }

        $relativePath = str_replace('\\', '/', ltrim((string) $path, '/'));
        $candidates = [
            public_path($relativePath),
            base_path($relativePath),
        ];

        if (strpos($relativePath, 'storage/') === 0) {
            $candidates[] = storage_path('app/public/' . substr($relativePath, strlen('storage/')));
        }

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    };

    $dibuatSignature = $signaturePath($jsa->dibuat_signature ?? null);
    $disetujuiSignature = $signaturePath($jsa->disetujui_signature ?? null);
    $diverifikasiSignature = $signaturePath($jsa->diverifikasi_signature ?? null);
    $tanggal = $jsa->tanggal ? \Carbon\Carbon::parse($jsa->tanggal)->format('d-m-Y') : '-';
@endphp

<!DOCTYPE html>
<html>
<head>
    <title>JSA PDF</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 92px 24px 28px 24px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #111;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            line-height: 1.25;
        }

        header {
            position: fixed;
            top: -72px;
            left: 0;
            right: 0;
            height: 66px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #111;
            padding: 4px 5px;
            vertical-align: top;
        }

        .header-table,
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .logo-cell {
            width: 14%;
        }

        .header-logo {
            height: 52px;
            max-width: 78px;
        }

        .header-title {
            text-align: center;
            line-height: 1.35;
        }

        .company-title {
            font-size: 14px;
            font-weight: bold;
        }

        .document-title {
            font-size: 13px;
            font-weight: bold;
        }

        .meta-table td {
            height: 22px;
        }

        .meta-label {
            font-weight: bold;
        }

        .signature-table {
            margin-top: 6px;
        }

        .signature-table th {
            text-align: center;
            font-weight: bold;
            background: #f2f2f2;
            vertical-align: middle;
        }

        .signature-box td {
            height: 72px;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-role td {
            text-align: center;
            padding: 3px 5px;
        }

        .signature-img {
            display: block;
            max-width: 150px;
            max-height: 48px;
            margin: 0 auto 2px auto;
        }

        .section-title {
            margin: 9px 0 6px 0;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
        }

        .jsa-section {
            page-break-before: always;
        }

        .jsa-table th {
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            background: #f2f2f2;
        }

        .jsa-table td {
            word-break: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
            vertical-align: middle;
        }

        .col-no {
            width: 4%;
            text-align: center;
            vertical-align: middle;
        }

        .col-langkah {
            width: 22%;
        }

        .col-bahaya {
            width: 27%;
        }

        .col-pengendalian {
            width: 47%;
        }

        .cell-text {
            line-height: 1.22;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <img src="{{ public_path('images/logo-st.png') }}" alt="Logo ST" class="header-logo">
                </td>
                <td class="header-title">
                    <div class="company-title">PT SEMEN TONASA</div>
                    <div>Utamakan Keselamatan dan Kesehatan Kerja</div>
                    <div class="document-title">Job Safety Analysis</div>
                </td>
                <td class="logo-cell" style="text-align: right;">
                    <img src="{{ public_path('images/logo-k3.png') }}" alt="Logo K3" class="header-logo">
                </td>
            </tr>
        </table>
    </header>

    <main>
        <table class="meta-table">
            <colgroup>
                <col style="width: 50%;">
                <col style="width: 50%;">
            </colgroup>
            <tr>
                <td><span class="meta-label">Nama Perusahaan:</span> {{ $jsa->nama_perusahaan ?: 'PT Semen Tonasa' }}</td>
                <td><span class="meta-label">Job Safety Analysis No:</span> {{ $jsa->no_jsa ?: '-' }}</td>
            </tr>
            <tr>
                <td><span class="meta-label">Nama JSA:</span> {{ $jsa->nama_jsa ? strtoupper($jsa->nama_jsa) : '-' }}</td>
                <td><span class="meta-label">Departemen:</span> {{ $jsa->departemen ?: '-' }}</td>
            </tr>
            <tr>
                <td><span class="meta-label">Area Kerja:</span> {{ $jsa->area_kerja ?: '-' }}</td>
                <td><span class="meta-label">Tanggal:</span> {{ $tanggal }}</td>
            </tr>
        </table>

        <table class="signature-table">
            <thead>
                <tr>
                    <th>Dibuat oleh</th>
                    <th>Disetujui oleh</th>
                    <th>Diverifikasi oleh</th>
                </tr>
            </thead>
            <tbody>
                <tr class="signature-box">
                    <td>
                        @if($dibuatSignature)
                            <img src="{{ $dibuatSignature }}" class="signature-img" alt="TTD Dibuat">
                        @endif
                        {{ $jsa->dibuat_nama ?: '-' }}
                    </td>
                    <td>
                        @if($disetujuiSignature)
                            <img src="{{ $disetujuiSignature }}" class="signature-img" alt="TTD Disetujui">
                        @endif
                        {{ $jsa->disetujui_nama ?: '-' }}
                    </td>
                    <td>
                        @if($diverifikasiSignature)
                            <img src="{{ $diverifikasiSignature }}" class="signature-img" alt="TTD Diverifikasi">
                        @endif
                        {{ $jsa->diverifikasi_nama ?: '-' }}
                    </td>
                </tr>
                <tr class="signature-role">
                    <td>Supervisor</td>
                    <td>Permit Issuer</td>
                    <td>Permit Verifikator</td>
                </tr>
            </tbody>
        </table>

        <div class="jsa-section">
            <h4 class="section-title">Langkah Kerja</h4>
            <table class="jsa-table">
                <colgroup>
                    <col class="col-no">
                    <col class="col-langkah">
                    <col class="col-bahaya">
                    <col class="col-pengendalian">
                </colgroup>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Urutan Langkah Kerja</th>
                        <th>Bahaya/Risiko</th>
                        <th>Pengendalian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($steps as $index => $item)
                        @php($row = (array) $item)
                        <tr>
                            <td class="col-no">{{ $index + 1 }}</td>
                            <td><div class="cell-text">{!! $formatCell($row['langkah'] ?? null) !!}</div></td>
                            <td><div class="cell-text">{!! $formatCell($row['bahaya'] ?? null) !!}</div></td>
                            <td><div class="cell-text">{!! $formatCell($row['pengendalian'] ?? null) !!}</div></td>
                        </tr>
                    @empty
                        <tr>
                            <td class="col-no">-</td>
                            <td colspan="3" style="text-align: center;">Belum ada data langkah kerja.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
