<!DOCTYPE html>
<html>
<head>
    <title>JSA PDF</title>
    <style>
        @page {
            margin-top: 120px;
        }
        header {
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
            height: 100px;
            text-align: center;
        }
        body { font-family: sans-serif; font-size: 11px; }
        table, th, td { border: 1px solid black; border-collapse: collapse; padding: 4px; }
        .header-table, .header-table td { border: none; }
        .section-title { text-align: center; font-weight: bold; font-size: 14px; margin-bottom: 5px; }
        .signature-table td { height: 60px; vertical-align: bottom; text-align: center; }
        .no-border { border: none; }
    </style>
</head>
<body>

    {{-- HEADER FIXED --}}
    <header>
        <table class="header-table" width="100%">
            <tr>
                <td style="width: 15%;">
                    <img src="{{ public_path('images/logo-st.png') }}" alt="Logo ST" style="height: 60px;">
                </td>
                <td style="text-align: center;">
                    <div style="font-size: 14px; font-weight: bold;">PT SEMEN TONASA</div>
                    <div>Utamakan Keselamatan dan Kesehatan Kerja</div>
                    <div style="font-size: 13px; font-weight: bold;">Job Safety Analysis</div>
                </td>
                <td style="width: 15%; text-align: right;">
                    <img src="{{ public_path('images/logo-k3.png') }}" alt="Logo K3" style="height: 60px;">
                </td>
            </tr>
        </table>
    </header>

    {{-- CONTENT --}}
    <main>
        <table width="100%">
            <tr>
                <td><strong>PT Semen Tonasa</strong></td>
                <td><strong>Job Safety Analysis No:</strong> {{ $jsa->no_jsa }}</td>
            </tr>
            <tr>
                <td><strong>JSA {{ strtoupper($jsa->nama_jsa) }}</strong></td>
                <td><strong>Departemen:</strong> {{ $jsa->departemen }}</td>
            </tr>
            <tr>
                <td><strong>Area Kerja:</strong> {{ $jsa->area_kerja }}</td>
                <td><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($jsa->tanggal)->format('d-m-Y') }}</td>
            </tr>
        </table>

        <table width="100%" style="margin-top: 5px;">
            <tr>
                <td class="no-border" style="text-align: center;"><strong>Dibuat oleh</strong></td>
                <td class="no-border" style="text-align: center;"><strong>Disetujui oleh</strong></td>
                <td class="no-border" style="text-align: center;"><strong>Diverifikasi oleh</strong></td>
            </tr>
            <tr class="signature-table">
                <td>
                    @if($jsa->dibuat_signature && file_exists($jsa->dibuat_signature))
                        <img src="file://{{ $jsa->dibuat_signature }}" style="height: 60px;">
                    @endif
                    <br>{{ $jsa->dibuat_nama }}
                </td>
                <td>
                    @if($jsa->disetujui_signature && file_exists($jsa->disetujui_signature))
                        <img src="file://{{ $jsa->disetujui_signature }}" style="height: 60px;">
                    @endif
                    <br>{{ $jsa->disetujui_nama }}
                </td>
                <td>
                    @if($jsa->diverifikasi_signature && file_exists($jsa->diverifikasi_signature))
                        <img src="file://{{ $jsa->diverifikasi_signature }}" style="height: 60px;">
                    @endif
                    <br>{{ $jsa->diverifikasi_nama }}
                </td>
            </tr>
            <tr>
                <td class="no-border" style="text-align: center;">Supervisor</td>
                <td class="no-border" style="text-align: center;">Permit Issuer</td>
                <td class="no-border" style="text-align: center;">Permit Verifikator</td>
            </tr>
        </table>

        <h4 class="section-title">Langkah Kerja</h4>
        <table width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Urutan Langkah Kerja</th>
                    <th>Bahaya/Risiko</th>
                    <th>Pengendalian</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jsa->langkah_kerja as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['langkah'] }}</td>
                    <td>{{ $item['bahaya'] }}</td>
                    <td>{{ $item['pengendalian'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>

</body>
</html>
