<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Izin Kerja Umum</title>
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
{{-- HEADER --}}
<table class="header-table">
    <tr>
        <td style="width: 20%;">
            <img src="{{ public_path('images/logo-st.png') }}" alt="Logo" height="50">
        </td>
        <td style="width: 60%; text-align: center;">
            <h2 style="margin: 0;">IZIN KERJA MENGANGKAT BEBAN</h2>
        </td>
        <td style="width: 20%; text-align: right;">
            <span style="font-weight: bold;">Nomor:</span> <span style="color: gray;">Jika ada</span>
        </td>
    </tr>
</table>

<p style="text-align: justify; font-size: 11px; margin-top: 5px;">
    Izin kerja ini diberikan untuk semua pekerjaan mengangkat beban dengan pesawat angkat bergerak misalnya
    <i>mobile crane</i> dan lain-lain, pengangkatan diluar rutinitas dengan pesawat angkat tetap berenergi seperti
    <i>overhead/hoist crane</i>, <i>hoist winch</i> dan lain-lain, pengangkatan tandem. Pekerjaan tidak bisa dimulai hingga izin kerja
    di verifikasi oleh <i>Permit Verificator</i>, diterbitkan oleh <i>Permit Issuer</i>, disahkan oleh <i>Permit Authorizer</i> dan
    <i>major hazards & control</i> disosialisasikan oleh <i>Permit Receiver</i>.
</p>

{{-- BAGIAN 1 --}}
<div class="section-title">1. Detail Pekerjaan</div>
<table class="table">
    <tr>
        <th style="width: 50%;">Lokasi pekerjaan:</th>
        <th style="width: 50%;">Tanggal:</th>
    </tr>
    <tr>
        <td style="height: 25px;"></td>
        <td></td>
    </tr>
    <tr>
        <th colspan="2">Uraian pekerjaan:</th>
    </tr>
    <tr>
        <td colspan="2" style="height: 50px;"></td>
    </tr>
    <tr>
        <th colspan="2">Peralatan/perlengkapan yang akan digunakan pada pekerjaan:</th>
    </tr>
    <tr>
        <td colspan="2" style="height: 50px;"></td>
    </tr>
    <tr>
        <th style="width: 50%;">Perkiraan jumlah pekerja yang akan terlibat dalam pekerjaan ini:</th>
        <th style="width: 50%;">Nomor gawat darurat yang harus dihubungi saat darurat:</th>
    </tr>
    <tr>
        <td style="height: 25px;"></td>
        <td></td>
    </tr>
</table>
{{-- 2. Dokumentasi Persyaratan Pengangkatan Beban --}}
<table class="table">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold;">
                2. Dokumentasi Persyaratan Pengangkatan Beban
            </th>
            <th style="background-color: black; color: white; text-align: center;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="width: 5%;">•</td>
            <td><i>Operator</i> pesawat angkat memiliki lisensi K3 yang masih berlaku dan sesuai (lampirkan).</td>
            <td style="text-align: center;">Ya / N/A</td>
        </tr>
        <tr>
            <td>•</td>
            <td>Juru ikat (<i>Rigger</i>) memiliki lisensi K3 yang masih berlaku (lampirkan).</td>
            <td style="text-align: center;">Ya / N/A</td>
        </tr>
        <tr>
            <td>•</td>
            <td>Pesawat angkat memiliki sertifikat uji kelayakan yang masih berlaku (lampirkan).</td>
            <td style="text-align: center;">Ya / N/A</td>
        </tr>
        <tr>
            <td>•</td>
            <td><i>Load chart</i> pengangkatan tersedia, di unit <i>control display/hardcopy</i>.</td>
            <td style="text-align: center;">Ya / N/A</td>
        </tr>
        <tr>
            <td>•</td>
            <td>Rencana pengangkatan telah dibuat oleh <i>Rigger</i> dan <i>Operator</i> dan disetujui oleh <i>Lifting Permit Verificator</i> (bisa lampiran terpisah).</td>
            <td style="text-align: center;">Ya / N/A</td>
        </tr>
        <tr>
            <td>•</td>
            <td><i>Job Safety Analysis/Safe Working Procedure</i> sudah tersedia dan semua pengendalian bahayanya sudah dilakukan (lampirkan).</td>
            <td style="text-align: center;">Ya / N/A</td>
        </tr>
    </tbody>
</table>
{{-- 3. Persyaratan Kerja Aman --}}
<table class="table">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold;">
                3. Persyaratan Kerja Aman
            </th>
            <th style="background-color: black; color: white; text-align: center;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $items = [
                'Area pengangkatan sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
                'Area pengangkatan sudah dibatasi dengan memasang barikade atau safety line dan rambu peringatan.',
                'Area pengangkatan sudah diamankan dari potensi jatuhan benda/material.',
                'Area pengangkatan sudah diamankan dari potensi pekerja terpeleset/tersandung.',
                'Area pengangkatan bebas/dalam jarak aman dari kabel listrik bertegangan tinggi',
                'Radius kerja pesawat angkat sudah diamankan.',
                'Sudah tersedia standby person untuk mengamankan area pengangkatan dan memahami tanggung jawabnya.',
                'Komunikasi antara Rigger dan Operator menggunakan radio komunikasi/handphone/sinyal tangan.',
                'Operator dan Rigger sudah menentukan kode sinyal tangan sebelum pengangkatan',
                'Aba-aba pengangkatan dilakukan oleh Rigger yang memiliki lisensi K3 yang masih berlaku.',
                'Helper pekerjaan pengangkatan kompeten terhadap pekerjaan yang dilakukan.',
                'Alat pelindung diri yang akan dipakai sudah diperiksa dan dinyatakan layak untuk dipakai.',
                'Semua pekerja pengangkatan sudah menggunakan alat pelindung diri yang sesuai.',
                'Semua pekerja terkait memahami bahwa dilarang berada di bawah beban yang sedang diangkat',
                'Semua pekerja terkait memahami dilarang naik pada beban yang sedang diangkat.',
                'Pekerja yang bekerja di dalam man basket, sudah dinyatakan fit untuk bekerja di ketinggian.',
                'Pesawat angkat ditempatkan pada permukaan yang datar, keras dan rata.',
                'Pesawat angkat yang akan digunakan sudah diperiksa ulang dan dipastikan layak pakai.',
                'Semua outrigger telah dikeluarkan maksimal diatas bantalan kayu/besi',
                'Counterweight pesawat angkat dipasang sebelum pengangkatan dilakukan.',
                'Pesawat angkat dilengkapi dengan limit switch dan berfungsi baik.',
                'Block hook pesawat angkat memiliki safety latch dan berfungsi baik.',
                'Sudah dilakukan load test yang sesuai pada pesawat angkat hoist winch.',
                'Alat bantu angkat yang akan digunakan sudah diperiksa dan dinyatakan layak untuk digunakan, tertera SWL.',
                'Pasang tali pandu pengaruh (tag line) pada beban yang akan diangkat.',
                'Man basket sudah diperiksa, dipastikan layak dan aman untuk digunakan, pintu mengarah kedalam, ada pengunci pintu, ada anchor point untuk hook FBH, ada tag line.',
                'Tertata informasi SWL dan jumlah maksimum orang yang bisa diangkat pada man basket, dan berwarna kontras.',
                'Maksimum hanya 2 pekerja yang boleh diangkat dalam man basket, kecuali ditentukan lain oleh orang yang kompeten.',
                'Remote control hoist/overhead crane dan area pengangkatan mempunyai wind direction yang jelas.',
            ];
        @endphp

        @foreach ($items as $text)
        <tr>
            <td style="width: 5%;">•</td>
            <td>{!! preg_replace('/(Rigger|Operator|man basket|load test|outrigger|wind direction|limit switch|counterweight|tag line|SWL|safety line|safety latch|anchor point|control display|hardcopy|handphone)/i', '<i>$1</i>', $text) !!}</td>
            <td style="text-align: center;">Ya / N/A</td>
        </tr>
        @endforeach
    </tbody>
</table>
<table class="table">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold;">
                4. Rekomendasi Persyaratan Kerja Aman Tambahan dari <i>Permit Verificator/Permit Issuer</i> (Jika ada)
            </th>
            <th style="background-color: black; color: white; text-align: center;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2" style="height: 100px;"></td>
            <td style="text-align: center;">Ya / N/A</td>
        </tr>
    </tbody>
</table>
<table class="table">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold;">
                5. Permohonan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="font-style: italic; font-weight: bold;">Permit Requestor:</td>
        </tr>
        <tr>
            <td colspan="4">
                Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk dapat melakukan pekerjaan ini.
            </td>
        </tr>
        <tr style="text-align: center; font-weight: bold;">
            <td>Nama:</td>
            <td>Tanda tangan:</td>
            <td>Tanggal:</td>
            <td>Jam:</td>
        </tr>
        <tr>
            <td style="height: 40px;"></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
<table class="table">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold;">
                6. Verifikasi Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="font-style: italic; font-weight: bold;">Lifting Permit Verificator:</td>
        </tr>
        <tr>
            <td colspan="4">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua dokumentasi persyaratan pengangkatan beban,
                persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi serta saya telah menyetujui rencana pengangkatan untuk pekerjaan ini dapat dilakukan.
            </td>
        </tr>
        <tr>
            <td style="text-align: center;"><strong>Nama:</strong></td>
            <td style="text-align: center;"><strong>Tanda tangan:</strong></td>
            <td style="text-align: center;"><strong>Tanggal:</strong></td>
            <td style="text-align: center;"><strong>Jam:</strong></td>
        </tr>
        <tr>
            <td style="height: 50px;"></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
<table class="table">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold;">
                7. Penerbitan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="font-style: italic; font-weight: bold;">Permit Issuer:</td>
        </tr>
        <tr>
            <td colspan="4">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan/atau rekomendasi persyaratan kerja aman tambahan dari 
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
            </td>
        </tr>
        <tr>
            <td style="text-align: center;"><strong>Nama:</strong></td>
            <td style="text-align: center;"><strong>Tanda tangan:</strong></td>
            <td style="text-align: center;"><strong>Tanggal:</strong></td>
            <td style="text-align: center;"><strong>Jam:</strong></td>
        </tr>
        <tr>
            <td style="height: 50px;"></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="4">
                Izin kerja ini berlaku dari tanggal: &nbsp; <u> / </u> &nbsp; <u> / </u> &nbsp; jam: <u>______</u> &nbsp; sampai tanggal: &nbsp; <u> / </u> &nbsp; <u> / </u> &nbsp; jam: <u>______</u>
            </td>
        </tr>
    </tbody>
</table>
<table class="table">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold;">
                8. Pengesahan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="font-style: italic; font-weight: bold;">Permit Authorizer:</td>
        </tr>
        <tr>
            <td colspan="4">
                Saya menyatakan bahwa saya telah memeriksa area kerja, semua dokumentasi persyaratan pengangkatan beban telah dilengkapi, semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah menekankan apa saja 
                <i>major hazards</i> dan pengendaliannya yang harus disosialisasikan oleh <i>Permit Receiver</i> kepada seluruh pekerja terkait.
            </td>
        </tr>
        <tr>
            <td style="text-align: center;"><strong>Nama:</strong></td>
            <td style="text-align: center;"><strong>Tanda tangan:</strong></td>
            <td style="text-align: center;"><strong>Tanggal:</strong></td>
            <td style="text-align: center;"><strong>Jam:</strong></td>
        </tr>
        <tr>
            <td style="height: 50px;"></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
<table class="table">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold;">
                9. Pelaksanaan Pekerjaan
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="font-style: italic; font-weight: bold;">Permit Receiver:</td>
        </tr>
        <tr>
            <td colspan="4">
                Saya menyatakan bahwa semua dokumentasi persyaratan pengangkatan beban telah dilengkapi, semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah mensosialisasikan apa saja 
                <i>major hazards</i> dan pengendaliannya dari pekerjaan ini kepada seluruh pekerja terkait.
            </td>
        </tr>
        <tr>
            <td style="text-align: center;"><strong>Nama:</strong></td>
            <td style="text-align: center;"><strong>Tanda tangan:</strong></td>
            <td style="text-align: center;"><strong>Tanggal:</strong></td>
            <td style="text-align: center;"><strong>Jam:</strong></td>
        </tr>
        <tr>
            <td style="height: 50px;"></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
<table class="table" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th colspan="3" style="background-color: black; color: white; font-weight: bold; padding: 4px;">
                10. Penutupan Izin Kerja
            </th>
            <th style="background-color: black; color: white; text-align: center; width: 10%;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="width: 25%; font-style: italic; padding: 4px;"><strong>Lock & Tag</strong></td>
            <td colspan="2" style="padding: 4px;">Semua <i>lock & tag</i> sudah dilepas</td>
            <td style="text-align: center;">☐ Ya &nbsp;&nbsp; ☐ N/A</td>
        </tr>
        <tr>
            <td style="font-style: italic; padding: 4px;"><strong>Sampah & Peralatan Kerja</strong></td>
            <td colspan="2" style="padding: 4px;">Semua sampah sudah dibersihkan dan peralatan kerja sudah diamankan</td>
            <td style="text-align: center;">☐ Ya &nbsp;&nbsp; ☐ N/A</td>
        </tr>
        <tr>
            <td style="font-style: italic; padding: 4px;"><strong>Machine Guarding</strong></td>
            <td colspan="2" style="padding: 4px;">Semua <i>machine guarding</i> sudah dipasang kembali</td>
            <td style="text-align: center;">☐ Ya &nbsp;&nbsp; ☐ N/A</td>
        </tr>
        <tr>
            <th style="padding: 4px;">Tanggal:</th>
            <th style="padding: 4px;">Jam:</th>
            <th colspan="2" style="text-align: center; padding: 4px;">Tanda Tangan</th>
        </tr>
        <tr>
            <td style="height: 35px;"></td>
            <td></td>
            <td style="text-align: center;"><i>Permit Requestor</i></td>
            <td style="text-align: center;"><i>Permit Issuer</i></td>
        </tr>
    </tbody>
</table>

</body>
</html>