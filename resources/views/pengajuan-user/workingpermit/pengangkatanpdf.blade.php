<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rencana Pengangkatan</title>
    <style>
        @page { size: A4; margin: 10mm; }
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 11px;
            line-height: 1.45;
            color: #111;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }
        .section-title {
            background: #000;
            color: #fff;
            font-weight: bold;
            padding: 6px;
            font-size: 12px;
        }
        .text-center {
            text-align: center;
        }
        .label {
            font-weight: bold;
        }
        .muted {
            color: #555;
        }
        .signature {
            max-height: 55px;
            max-width: 140px;
        }
        .diagram {
            max-width: 100%;
            max-height: 260px;
        }
        .no-border {
            border: none;
        }
    </style>
</head>
<body>
@php
    $teknikPengikatan = is_array($permit->teknik_pengikatan ?? null)
        ? $permit->teknik_pengikatan
        : json_decode($permit->teknik_pengikatan ?? '[]', true);

    $wireRopeSling = is_array($permit->wire_rope_sling ?? null)
        ? $permit->wire_rope_sling
        : json_decode($permit->wire_rope_sling ?? '[]', true);

    $teknikPengikatan = is_array($teknikPengikatan) ? $teknikPengikatan : [];
    $wireRopeSling = is_array($wireRopeSling) ? $wireRopeSling : [];

    $fmtList = function (array $items) {
        return empty($items) ? '-' : implode(', ', $items);
    };

    $showImage = function (?string $path) {
        return $path && file_exists(public_path($path));
    };
@endphp

<table>
    <tr>
        <td style="width: 20%;" class="text-center">
            <img src="file://{{ public_path('images/logo-st.png') }}" alt="Logo" style="width: 55%; height: auto;">
        </td>
        <td class="text-center">
            <div style="font-size: 16px; font-weight: bold;">RENCANA PENGANGKATAN</div>
            <div class="muted">Working Permit Pengangkatan</div>
        </td>
        <td style="width: 24%;">
            <span class="label">Notification ID:</span><br>
            {{ $permit->notification_id }}
        </td>
    </tr>
    <tr>
        <td colspan="3" style="font-size: 10px;">
            Rencana pengangkatan dibuat sebelum memulai kegiatan pengangkatan, dibuat oleh Operator Pesawat Angkat dan Juru Ikat (Rigger),
            dan disetujui oleh Lifting Permit Verificator.
        </td>
    </tr>
</table>

<div class="section-title">1. Pesawat Angkat, Alat Bantu Angkat & Berat Beban</div>
<table>
    <tr>
        <td class="label" style="width: 22%;">Jenis / Tipe</td>
        <td style="width: 28%;">{{ $permit->jenis_tipe ?? '-' }}</td>
        <td class="label" style="width: 25%;">Maksimum panjang boom</td>
        <td>{{ $permit->max_boom ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Kapasitas maksimum</td>
        <td>{{ $permit->kapasitas_maks ?? '-' }}</td>
        <td class="label">Minimum panjang boom</td>
        <td>{{ $permit->min_boom ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">SWL main block</td>
        <td>{{ $permit->swl_main_block ?? '-' }}</td>
        <td class="label">Maksimum radius boom</td>
        <td>{{ $permit->max_radius ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">SWL auxiliary block</td>
        <td>{{ $permit->swl_aux_block ?? '-' }}</td>
        <td class="label">Minimum radius boom</td>
        <td>{{ $permit->min_radius ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">SWL chain / wire rope / webbing sling</td>
        <td>{{ $permit->swl_chain ?? '-' }}</td>
        <td class="label">SWL master link / oblong ring</td>
        <td>{{ $permit->swl_master_link ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">SWL shackle</td>
        <td>{{ $permit->swl_shackle ?? '-' }}</td>
        <td class="label">SWL hammer lock</td>
        <td>{{ $permit->swl_hammer_lock ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">SWL spreader bar</td>
        <td>{{ $permit->swl_spreader_bar ?? '-' }}</td>
        <td class="label">SWL hook</td>
        <td>{{ $permit->swl_hook ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">SWL pulley</td>
        <td>{{ $permit->swl_pulley ?? '-' }}</td>
        <td class="label">SWL anchor point</td>
        <td>{{ $permit->swl_anchor ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Berat beban</td>
        <td>{{ $permit->berat_beban ?? '-' }}</td>
        <td class="label">Berat man basket</td>
        <td>{{ $permit->berat_man_basket ?? '-' }}</td>
    </tr>
</table>

<div class="section-title">2. Teknik Pengikatan Beban dengan Webbing Sling</div>
<table>
    <tr>
        <td class="label" style="width: 35%;">Pilihan yang dipilih</td>
        <td>{{ $fmtList($teknikPengikatan) }}</td>
    </tr>
</table>

<div class="section-title">3. Teknik Pengikatan Beban dengan Wire Rope Sling</div>
<table>
    <tr>
        <td class="label" style="width: 35%;">Pilihan yang dipilih</td>
        <td>{{ $fmtList($wireRopeSling) }}</td>
    </tr>
</table>

<div class="section-title">4. Gambar / Sketsa Pengangkatan</div>
<table>
    <tr>
        <td class="text-center" style="width: 50%;">
            <div class="label" style="margin-bottom: 6px;">Diagram konfigurasi pesawat angkat</div>
            @if ($showImage($permit->diagram_pesawat))
                <img src="{{ public_path($permit->diagram_pesawat) }}" alt="Diagram Pesawat" class="diagram">
            @else
                -
            @endif
        </td>
        <td class="text-center" style="width: 50%;">
            <div class="label" style="margin-bottom: 6px;">Diagram konfigurasi pengikatan beban</div>
            @if ($showImage($permit->diagram_pengikatan))
                <img src="{{ public_path($permit->diagram_pengikatan) }}" alt="Diagram Pengikatan" class="diagram">
            @else
                -
            @endif
        </td>
    </tr>
</table>

<div class="section-title">5. Persetujuan Rencana Pengangkatan</div>
<table>
    <tr>
        <td colspan="3">
            Saya menyatakan bahwa rencana pengangkatan ini sudah sesuai dan aman untuk melakukan pengangkatan.
        </td>
    </tr>
    <tr class="text-center">
        <td class="label">Dibuat oleh Rigger</td>
        <td class="label">Dibuat oleh Operator</td>
        <td class="label">Disetujui oleh Lifting Permit Verificator</td>
    </tr>
    <tr class="text-center">
        <td>
            <div>{{ $permit->rigger_name ?? '-' }}</div>
            @if ($showImage($permit->signature_rigger))
                <img src="{{ public_path($permit->signature_rigger) }}" alt="TTD Rigger" class="signature">
            @endif
        </td>
        <td>
            <div>{{ $permit->operator_name ?? '-' }}</div>
            @if ($showImage($permit->signature_operator))
                <img src="{{ public_path($permit->signature_operator) }}" alt="TTD Operator" class="signature">
            @endif
        </td>
        <td>
            <div>{{ $permit->verificator_name ?? '-' }}</div>
            @if ($showImage($permit->signature_verificator))
                <img src="{{ public_path($permit->signature_verificator) }}" alt="TTD Verificator" class="signature">
            @endif
        </td>
    </tr>
</table>

</body>
</html>
