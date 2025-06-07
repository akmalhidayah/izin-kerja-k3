<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Izin Kerja</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; margin: 40px; line-height: 1.6; }
        h1, h2, h3, p { margin: 0; padding: 0; }
        .header-table { width: 100%; margin-bottom: 20px; }
        .header-table td { vertical-align: middle; }
        .content { text-align: justify; }
        .signature { text-align: right; margin-top: 50px; }
        .signature p { margin: 3px 0; }
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
@endphp
    {{-- HEADER FIXED --}}
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 15%;">
                    <img src="{{ public_path('images/logo-st.png') }}" alt="Logo ST" style="height: 60px;">
                </td>
                <td style="text-align: center;">
                    <div style="font-size: 14px; font-weight: bold;">PT. SEMEN TONASA</div>
                    <div>Utamakan Keselamatan dan Kesehatan Kerja</div>
                    <div style="font-size: 13px; font-weight: bold; text-decoration: underline;">Surat Izin Kerja</div>
                    <div style="font-size: 12px;">No. {{ $notification->id }} / 22.4.0/SIK/ST/{{ $bulanSaatIni }} / {{ date('Y') }}</div>
                </td>
                <td style="width: 15%; text-align: right;">
                    <img src="{{ public_path('images/logo-k3.png') }}" alt="Logo K3" style="height: 60px;">
                </td>
            </tr>
        </table>
    </header>

    {{-- Konten Surat --}}
    <div class="content">
        <p>Sesuai Prosedur Izin Kerja bagi kontraktor dengan mengacu pada prosedur No. Dokumen 22.4.0/P/05, setelah dilakukan pemeriksaan kelengkapan dokumen dan alat Keselamatan Kerja bagi Vendor/Kontraktor pada tanggal <strong>{{ now()->format('d F Y') }}</strong> maka dengan ini dinyatakan bahwa:</p>

        <br>

        <p><strong>Kontraktor:</strong> {{ strtoupper($notification->user->name ?? '-') }}</p>
        <p><strong>Jenis Pekerjaan:</strong> {{ strtoupper($dataKontraktor->jenis_pekerjaan ?? '-') }}</p>

        <br>

        <p>Telah memenuhi persyaratan Keselamatan dan Kesehatan Kerja (K3) dan <strong>Diizinkan</strong> melakukan pekerjaan yang ditunjuk oleh PT. Semen Tonasa sesuai <strong>No. PO:</strong> {{ $notification->number ?? '-' }}, terhitung tanggal <strong>{{ \Carbon\Carbon::parse($dataKontraktor->tanggal_mulai)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($dataKontraktor->tanggal_selesai)->translatedFormat('d F Y') }}</strong>.</p>

        <br>

        <p>Demikian Surat Izin Kerja ini diberikan untuk dipergunakan sebagaimana mestinya kepada Perusahaan di atas dan tidak diperkenankan untuk dipindahtangankan kepada pihak lain.</p>
    </div>

   <div class="signature">
    <p>Tonasa, {{ now()->translatedFormat('d F Y') }}</p>
    
    @if($sikStep && $sikStep->signature_senior_manager)
        <img src="{{ public_path($sikStep->signature_senior_manager) }}" alt="TTD Senior Manager" style="height: 60px; margin-top: 20px;">
    @else
        <br><br><br>
    @endif

    <p><strong>M. ALIANTO M., ST</strong></p>
    <p>SJP/Surat Izin Kerja</p>
</div>

</body>
</html>
