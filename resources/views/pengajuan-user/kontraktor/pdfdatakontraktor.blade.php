@php
    $tenagaKerja = isset($data->tenaga_kerja) ? json_decode($data->tenaga_kerja, true) : [];
    $peralatan = isset($data->peralatan_kerja) ? json_decode($data->peralatan_kerja, true) : [];
    $apd = isset($data->apd) ? json_decode($data->apd, true) : [];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Data Kontraktor</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 10px; }
        th, td { border: 1px solid #222; padding: 3px 7px; font-size: 11px; }
        th { background: #f3f3f3; }
        .noborder td { border: none !important; }
        .title { text-align: center; font-weight: bold; font-size: 17px; padding: 8px 0 3px 0;}
        .subhead { font-weight: bold; background: #e6e6e6; }
        .footer { font-size: 10px; margin-top: 10px;}
        .sign-box { height: 50px; }
        .ttd-img { height: 40px; }
        .italic { font-style: italic; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
   <header>
        <table class="header-table" width="100%">
            <tr>
                <td style="width: 15%;">
                    <img src="{{ public_path('images/logo-st.png') }}" alt="Logo ST" style="height: 60px;">
                </td>
                <td style="text-align: center;">
                    <div style="font-size: 18px; font-weight: bold;">FORM DATA KONTRAKTOR</div>
                </td>
                <td style="width: 15%; text-align: right;">
                    <img src="{{ public_path('images/logo-k3.png') }}" alt="Logo K3" style="height: 60px;">
                </td>
            </tr>
        </table>
    </header>
<table>
    <tr>
        <td style="width:28%;">NAMA PERUSAHAAN/KONTRAKTOR</td>
        <td colspan="2">{{ $data->nama_perusahaan ?? '-' }}</td>
    </tr>
    <tr>
        <td>JENIS/TYPE PEKERJAAN</td>
        <td colspan="2">{{ $data->jenis_pekerjaan ?? '-' }}</td>
    </tr>
    <tr>
        <td>LOKASI KERJA</td>
        <td colspan="2">{{ $data->lokasi_kerja ?? '-' }}</td>
    </tr>
    <tr>
        <td>JADWAL PEKERJAAN</td>
        <td colspan="2">
            {{ $data->tanggal_mulai ? \Carbon\Carbon::parse($data->tanggal_mulai)->format('d-m-Y') : '-' }}
            s/d
            {{ $data->tanggal_selesai ? \Carbon\Carbon::parse($data->tanggal_selesai)->format('d-m-Y') : '-' }}
        </td>
    </tr>
    <tr>
        <td>OP/SPK ATAU NOTIFIKASI</td>
        <td colspan="2">{{ $data->notification?->number ?? '-' }}</td>
    </tr>
</table>
{{-- 1. TENAGA KERJA --}}
<table>
    <tr class="subhead">
        <td colspan="3">1. TENAGA KERJA</td>
    </tr>
    <tr>
        <th>Nama Pekerja</th>
        <th style="width: 15%;">Jumlah</th>
        <th style="width: 25%;">Satuan</th>
    </tr>
    @php $totalTenagaKerja = 0; @endphp
    @forelse($tenagaKerja as $item)
        @if(isset($item['nama']) && $item['nama'])
        <tr>
            <td>{{ $item['nama'] }}</td>
            <td style="text-align: center;">{{ $item['jumlah'] ?? '-' }}</td>
            <td style="text-align: center;">{{ $item['satuan'] ?? '-' }}</td>
        </tr>
        @php $totalTenagaKerja += (int)($item['jumlah'] ?? 0); @endphp
        @endif
    @empty
        <tr>
            <td colspan="3" class="italic text-center">Tidak ada data tenaga kerja</td>
        </tr>
    @endforelse
    <tr class="subhead">
        <td style="text-align: right; font-weight: bold;">JUMLAH</td>
        <td style="text-align: center; font-weight: bold;">{{ $totalTenagaKerja }}</td>
        <td></td>
    </tr>
</table>

{{-- 2. PERALATAN KERJA --}}
<table>
    <tr class="subhead">
        <td colspan="3">2. PERALATAN KERJA</td>
    </tr>
    <tr>
        <th>Nama Peralatan</th>
        <th style="width: 15%;">Jumlah</th>
        <th style="width: 25%;">Satuan</th>
    </tr>
    @php $totalPeralatan = 0; @endphp
    @forelse($peralatan as $item)
        @if(isset($item['nama']) && $item['nama'])
        <tr>
            <td>{{ $item['nama'] }}</td>
            <td style="text-align: center;">{{ $item['jumlah'] ?? '-' }}</td>
            <td style="text-align: center;">{{ $item['satuan'] ?? '-' }}</td>
        </tr>
        @php $totalPeralatan += (int)($item['jumlah'] ?? 0); @endphp
        @endif
    @empty
        <tr>
            <td colspan="3" class="italic text-center">Tidak ada data peralatan kerja</td>
        </tr>
    @endforelse
    <tr class="subhead">
        <td style="text-align: right; font-weight: bold;">JUMLAH</td>
        <td style="text-align: center; font-weight: bold;">{{ $totalPeralatan }}</td>
        <td></td>
    </tr>
</table>

{{-- 3. PERALATAN KERJA (APD) --}}
<table>
    <tr class="subhead">
        <td colspan="3">3. PERALATAN KERJA (APD)</td>
    </tr>
    <tr>
        <th>Nama APD</th>
        <th style="width: 15%;">Jumlah</th>
        <th style="width: 25%;">Satuan</th>
    </tr>
    @php $totalApd = 0; @endphp
    @forelse($apd as $item)
        @if(isset($item['nama']) && $item['nama'])
        <tr>
            <td>{{ $item['nama'] }}</td>
            <td style="text-align: center;">{{ $item['jumlah'] ?? '-' }}</td>
            <td style="text-align: center;">{{ $item['satuan'] ?? '-' }}</td>
        </tr>
        @php $totalApd += (int)($item['jumlah'] ?? 0); @endphp
        @endif
    @empty
        <tr>
            <td colspan="3" class="italic text-center">Tidak ada data APD</td>
        </tr>
    @endforelse
    <tr class="subhead">
        <td style="text-align: right; font-weight: bold;">JUMLAH</td>
        <td style="text-align: center; font-weight: bold;">{{ $totalApd }}</td>
        <td></td>
    </tr>
</table>

    <div class="footer italic">
        Catatan: Peralatan &amp; Alat Pelindung Diri (APD) ditambahkan bila tidak ada di atas/kolom.
    </div>
   
{{-- Tanda Tangan --}}
<table class="noborder" style="margin-top: 24px; width: 100%;">
    <tr>
        <td class="noborder" style="width:55%; text-align: left; vertical-align: top;">
            Mengetahui<br>
            <strong>{{ $data->manager_nama ?? 'Manager K3 Plant Site' }}</strong><br>
            @if(!empty($data->ttd_manager) && file_exists(public_path($data->ttd_manager)))
                <img src="{{ public_path($data->ttd_manager) }}" style="height:60px;"><br>
            @else
                <br><br>
            @endif
            <span style="font-size:11px;">Manager K3 Plant Site</span><br>

            @if(!empty($data->diverifikasi_signature) && file_exists(public_path($data->diverifikasi_signature)))
                <div style="margin-top: 4px; display: flex; align-items: center;">
                    <img src="{{ public_path($data->diverifikasi_signature) }}" style="height:20px; margin-right:4px;">
                    <span style="font-size:9px;">{{ $data->diverifikasi_nama ?? '' }}</span>
                </div>
            @endif
        </td>

        <td class="noborder" style="width:45%; text-align: left; vertical-align: top;">
            {{-- Tanggal di atas TTD Perusahaan --}}
            <div style="text-align: left; font-size: 10px; margin-bottom: 4px;">
                Tonasa, {{ $data->tanggal_selesai ? \Carbon\Carbon::parse($data->tanggal_selesai)->format('d F Y') : '__________' }}
            </div>
            <strong>{{ $data->perusahaan_nama ?? 'Perusahaan/Kontraktor' }}</strong><br>
            @if(!empty($data->ttd_perusahaan) && file_exists(public_path($data->ttd_perusahaan)))
                <img src="{{ public_path($data->ttd_perusahaan) }}" style="height:60px;"><br>
            @else
                <br><br>
            @endif
            <span style="font-size:11px;">Perusahaan/Kontraktor</span>
        </td>
    </tr>
</table>
</body>
</html>
