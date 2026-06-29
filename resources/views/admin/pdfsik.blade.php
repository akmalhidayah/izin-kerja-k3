<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Penyelesaian Pekerjaan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; margin: 40px 42px; line-height: 2; color: #111; }
        h1, h2, h3, p { margin: 0; padding: 0; }
        .header-table { width: 100%; margin-bottom: 34px; }
        .header-table td { vertical-align: middle; }
        .company-name { font-size: 16px; font-weight: 700; line-height: 1.2; margin-bottom: 7px; }
        .company-tagline { font-size: 11px; font-weight: 500; line-height: 1.25; margin-bottom: 8px; }
        .document-title { font-size: 14px; font-weight: 700; line-height: 1.25; text-decoration: underline; margin-bottom: 5px; }
        .document-number { font-size: 11px; font-weight: 500; line-height: 1.2; }
        .content { text-align: justify; }
        .content p.paragraph { text-indent: 34px; line-height: 2; margin-bottom: 12px; }
        .party-block { line-height: 1.8; margin: 16px 0 18px; }
        .signature { text-align: right; margin-top: 42px; line-height: 1.55; }
        .signature p { margin: 3px 0; line-height: 1.55; }
        .bold { font-weight: bold; }
        .italic { font-style: italic; }
        .center { text-align: center; }
    </style>
</head>
<body>
@php
    $bulanRomawi = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
    ];
    $bulanSaatIni = $bulanRomawi[now()->format('n')];
    $documentLabel = match (strtolower($notification->type ?? '')) {
        'po' => 'No. PO',
        'spk' => 'No. SPK',
        'notif' => 'No. Notification',
        default => 'No. Dokumen',
    };
@endphp
    {{-- HEADER FIXED --}}
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 15%;">
                    <img src="{{ public_path('images/logo-st.png') }}" alt="Logo ST" style="height: 60px;">
                </td>
                <td style="text-align: center;">
                    <div class="company-name">PT. SEMEN TONASA</div>
                    <div class="company-tagline">Utamakan Keselamatan dan Kesehatan Kerja</div>
                    <div class="document-title">Surat Penyelesaian Pekerjaan</div>
                    <div class="document-number">No. {{ $notification->id }} / 22.4.0/SIK/ST/{{ $bulanSaatIni }} / {{ date('Y') }}</div>
                </td>
                <td style="width: 15%; text-align: right;">
                    <img src="{{ public_path('images/logo-k3.png') }}" alt="Logo K3" style="height: 60px;">
                </td>
            </tr>
        </table>
    </header>

    {{-- Konten Surat --}}
    <div class="content">
        <p class="paragraph">Sesuai Prosedur Izin Kerja bagi kontraktor dengan mengacu pada prosedur No. Dokumen 22.4.0/P/05, setelah dilakukan pemeriksaan kelengkapan dokumen dan alat Keselamatan Kerja bagi Vendor/Kontraktor pada tanggal <strong>{{ \Carbon\Carbon::parse($sikStep->created_at)->translatedFormat('d F Y') }}</strong> maka dengan ini dinyatakan bahwa:</p>

        <div class="party-block">
            <p><strong>Kontraktor:</strong> {{ strtoupper($notification->user->name ?? '-') }}</p>
            <p><strong>Jenis Pekerjaan:</strong> {{ strtoupper($dataKontraktor->jenis_pekerjaan ?? '-') }}</p>
        </div>

        <p class="paragraph">Telah memenuhi persyaratan Keselamatan dan Kesehatan Kerja (K3) serta <strong>menyelesaikan</strong> pekerjaan yang ditunjuk oleh PT. Semen Tonasa sesuai <strong>{{ $documentLabel }}:</strong> {{ $notification->number ?? '-' }}, terhitung tanggal <strong>{{ \Carbon\Carbon::parse($dataKontraktor->tanggal_mulai)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($dataKontraktor->tanggal_selesai)->translatedFormat('d F Y') }}</strong>.</p>

        <p class="paragraph">Demikian Surat Penyelesaian Pekerjaan ini diberikan untuk dipergunakan sebagaimana mestinya kepada Perusahaan di atas dan tidak diperkenankan untuk dipindahtangankan kepada pihak lain.</p>
    </div>

    <div class="signature">
        <p>Tonasa, {{ \Carbon\Carbon::parse($sikStep->created_at)->translatedFormat('d F Y') }}</p>

        @if($sikStep && $sikStep->signature_senior_manager)
            <img src="{{ public_path($sikStep->signature_senior_manager) }}" alt="TTD Senior Manager" style="height: 60px; margin-top: 20px;">
        @else
            <br><br><br>
        @endif

        <p><strong>M. ALIANTO M., ST</strong></p>
        <div style="display: inline-flex; align-items: center; gap: 0;">
            <span style="margin-right: -2px;">SJP</span>
            @if($sikStep && $sikStep->signature_manager)
                <img src="{{ public_path($sikStep->signature_manager) }}" alt="TTD Manager" style="height: 26px; vertical-align: middle; margin: 0 -6px;">
            @endif
            <span style="margin-left: -2px;">/Surat Penyelesaian Pekerjaan</span>
        </div>
    </div>

</body>
</html>
