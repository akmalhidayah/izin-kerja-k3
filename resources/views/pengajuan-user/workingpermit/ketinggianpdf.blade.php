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
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <tr>
        <td style="border: 1px solid black; width: 20%; text-align: center;">
            <img src="{{ public_path('images/logo-tonasa.png') }}" alt="Logo Perusahaan" style="width: 80px; height: auto;">
            <div style="font-size: 12px;">Logo Perusahaan</div>
        </td>
        <td style="border: 1px solid black; text-align: center;" colspan="2">
            <h2 style="margin: 0;">IZIN KERJA</h2>
            <h3 style="margin: 0;">BEKERJA DI KETINGGIAN</h3>
        </td>
        <td style="border: 1px solid black; width: 25%;">
            <strong>Nomor:</strong> <span style="color: gray;">Jika ada</span>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="border: 1px solid black; font-size: 12px; padding: 5px; text-align: justify;">
            Izin kerja ini diberikan untuk pekerjaan di ketinggian yang memiliki risiko terjatuh dari ketinggian ≥ 1.8 m, penggunaan <i>man basket</i>/<i>man box</i> dengan <i>mobile crane</i>/<i>hoist winch</i> atau yang sejenis untuk mengangkat orang serta penggunaan gondola. Pekerjaan tidak bisa dimulai hingga izin kerja di verifikasi oleh <i>Permit Verificator</i>, diterbitkan oleh <i>Permit Issuer</i>, disahkan oleh <i>Permit Authorizer</i> dan <i>major hazards & control</i> disosialisasikan oleh <i>Permit Receiver</i>.
        </td>
    </tr>
</table>

<!-- Bagian 1: Detail Pekerjaan -->
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold; text-align: center; padding: 5px;">
                1. Detail Pekerjaan
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; width: 50%; padding: 5px;">Lokasi pekerjaan:</td>
            <td style="border: 1px solid black; font-weight: bold; padding: 5px;">Tanggal:</td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid black; font-weight: bold; padding: 5px;">Uraian pekerjaan:</td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid black; height: 60px;"></td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid black; font-weight: bold; padding: 5px;">Peralatan/perlengkapan yang akan digunakan pada pekerjaan:</td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid black; height: 60px;"></td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; padding: 5px;">Perkiraan jumlah pekerja yang akan terlibat dalam pekerjaan ini:</td>
            <td style="border: 1px solid black; font-weight: bold; padding: 5px;">Nomor gawat darurat yang harus dihubungi saat darurat:</td>
        </tr>
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="7" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                2. Daftar Pekerja dan Sketsa Pekerjaan <span style="font-weight: normal; font-size: 12px;">(bisa dalam lampiran terpisah)</span>
            </th>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 5px;">Nama</th>
            <th style="border: 1px solid black; padding: 5px;">Paraf</th>
            <th style="border: 1px solid black; padding: 5px;">Nama</th>
            <th style="border: 1px solid black; padding: 5px;">Paraf</th>
            <th style="border: 1px solid black; padding: 5px;">Nama</th>
            <th style="border: 1px solid black; padding: 5px;">Paraf</th>
            <th style="border: 1px solid black; padding: 5px; width: 25%; color: #ccc; text-align: center;">Jika Diperlukan</th>
        </tr>
    </thead>
    <tbody>
        @for ($i = 0; $i < 5; $i++)
        <tr>
            <td style="border: 1px solid black; height: 35px;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
        </tr>
        @endfor
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                3. Persyaratan Kerja Aman
            </th>
            <th style="background-color: black; color: white; text-align: center; width: 12%;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $items = [
                'Area kerja sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
                'Area kerja/area kerja dibawah sudah diamankan dari potensi jatuhan benda/material.',
                'Area kerja sudah diamankan dari potensi pekerja terpeleset/tersandung.',
                '<i>Job Safety Analysis/Safe Working Procedure</i> sudah tersedia dan semua pengendalian bahayanya sudah dilakukan.',
                'Tepi terbuka/bukaan di lantai sudah dipasang pagar pengaman sementara/ditutup yang mencegah pekerja terjatuh.',
                'Pekerja menggunakan full body harness yang difungsikan sebagai <i>fall restraint system</i>.',
                'Semua pekerja yang terlibat terlatih, memahami bahaya, risiko dan pengendaliannya untuk bekerja di ketinggian.',
                'Semua pekerja terlibat sudah dinyatakan fit untuk bekerja di ketinggian.',
                'Semua pekerja yang terlibat sudah menggunakan full body harness dan memahami cara penggunaanya dengan aman.',
                'Semua pekerja yang terlibat memahami dimana akan mengaitkan <i>hook</i> FBH nya saat bekerja di ketinggian.',
                'Semua pekerja terlibat memahami saat berpindah di ketinggian, <i>double hook</i> FBH nya dikaitkan secara bergantian.',
                'Semua pekerja terlibat memahami teknik <i>three point contact</i> saat naik/turun tangga.',
                '<i>Full body harness</i> yang akan dipakai sudah diperiksa dan dinyatakan layak dan aman untuk dipakai.',
                'Peralatan/perlengkapan kerja yang akan digunakan sudah diperiksa dan dipastikan layak dan aman untuk digunakan.',
                'Peralatan kerja genggam tangan yang berisiko jatuh diberi tali pengikat untuk dikaitkan dengan aman saat di ketinggian.',
                '<i>Man basket</i> sudah diperiksa, dipastikan layak dan aman untuk digunakan, pintu mengarah kedalam dan ada pengunci & <i>anchor point</i>.',
                'Gondola sudah diperiksa, dipastikan layak dan aman untuk digunakan, mempunyai surat izin alat dan operator kompeten.',
                '<i>Man basket</i>/gondola dilengkapi dengan <i>tag line</i> untuk mengendalikan pengangkatan.',
            ];
        @endphp

        @foreach ($items as $text)
        <tr>
            <td style="width: 5%; border: 1px solid black; text-align: center;">•</td>
            <td style="border: 1px solid black; padding: 5px;">{!! $text !!}</td>
            <td style="border: 1px solid black; text-align: center;">Ya / N/A</td>
        </tr>
        @endforeach
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; text-align: left; padding: 5px;">
                4. Rekomendasi Persyaratan Kerja Aman Tambahan dari <i>Permit Verificator/Permit Issuer</i> 
                <span style="font-size: 11px;">(jika ada)</span>
            </th>
            <th style="background-color: black; color: white; text-align: center; width: 10%;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2" style="border: 1px solid black; height: 100px;"></td>
            <td style="border: 1px solid black; text-align: center;">Ya / N/A</td>
        </tr>
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; text-align: left; padding: 5px;">
                5. Permohonan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="padding: 6px; border-left: 1px solid black; border-right: 1px solid black; font-style: italic; font-weight: bold;">
                Permit Requestor:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-size: 13px; padding: 5px; text-align: justify;">
                Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk dapat melakukan pekerjaan ini.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Nama:</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Tanda tangan:</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Tanggal:</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; height: 40px;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
        </tr>
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; text-align: left; padding: 5px;">
                6. Verifikasi Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="padding: 6px; border-left: 1px solid black; border-right: 1px solid black; font-style: italic; font-weight: bold;">
                Working at Height Permit Verificator:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-size: 13px; padding: 5px; text-align: justify;">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
                <strong> Berikut nama-nama pekerja yang diizinkan untuk bekerja di ketinggian:</strong>
            </td>
        </tr>
        <!-- Baris nama pekerja -->
        <tr>
            <td style="border: 1px solid black; height: 35px;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
        </tr>
        <tr>
            <td style="border: 1px solid black; height: 35px;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
        </tr>
        <!-- Tanda tangan -->
        <tr>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Nama:</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Tanda tangan:</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Tanggal:</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; height: 40px;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
        </tr>
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; text-align: left; padding: 5px;">
                7. Penerbitan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="padding: 6px; border-left: 1px solid black; border-right: 1px solid black; font-style: italic; font-weight: bold;">
                Permit Issuer:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-size: 13px; padding: 5px; text-align: justify;">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk pekerjaan ini dapat dilakukan.
            </td>
        </tr>
        <!-- Tanda tangan -->
        <tr>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Nama:</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Tanda tangan:</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Tanggal:</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; height: 40px;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
        </tr>
        <!-- Informasi masa berlaku -->
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 6px; font-size: 13px;">
                Izin kerja ini berlaku dari tanggal: &nbsp; &nbsp; / &nbsp; / &nbsp; jam: &nbsp; &nbsp; &nbsp; sampai tanggal &nbsp; / &nbsp; / &nbsp; jam:
            </td>
        </tr>
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; text-align: left; padding: 5px;">
                8. Pengesahan Izin Kerja
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="padding: 6px; border-left: 1px solid black; border-right: 1px solid black; font-style: italic; font-weight: bold;">
                Permit Authorizer:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-size: 13px; padding: 5px; text-align: justify;">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah menekankan apa saja 
                <i>major hazards</i> dan pengendaliannya yang harus disosialisasikan oleh <i>Permit Receiver</i> kepada seluruh pekerja terkait.
            </td>
        </tr>
        <!-- Tanda tangan -->
        <tr>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Nama:</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Tanda tangan:</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Tanggal:</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; height: 40px;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
        </tr>
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; text-align: left; padding: 5px;">
                9. Pelaksanaan Pekerjaan
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="padding: 6px; border-left: 1px solid black; border-right: 1px solid black; font-style: italic; font-weight: bold;">
                Permit Receiver:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-size: 13px; padding: 5px; text-align: justify;">
                Saya menyatakan bahwa semua persyaratan kerja aman yang telah ditentukan dan atau rekomendasi persyaratan kerja aman tambahan dari 
                <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk dapat melakukan pekerjaan ini serta saya sudah mensosialisasikan apa saja 
                <i>major hazards</i> dan pengendaliannya dari pekerjaan ini kepada seluruh pekerja terkait.
            </td>
        </tr>
        <!-- Tanda tangan -->
        <tr>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Nama:</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Tanda tangan:</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Tanggal:</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; height: 40px;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
        </tr>
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 13px;">
    <thead>
        <tr>
            <th colspan="3" style="background-color: black; color: white; text-align: left; padding: 5px;">
                10. Penutupan Izin Kerja
            </th>
            <th style="background-color: black; color: white; text-align: center;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">Lock & Tag</td>
            <td colspan="2" style="border: 1px solid black; padding: 5px;">Semua <i>lock & tag</i> sudah dilepas</td>
            <td style="border: 1px solid black; text-align: center;">Ya &nbsp;&nbsp; N/A</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">Sampah & Peralatan Kerja</td>
            <td colspan="2" style="border: 1px solid black; padding: 5px;">Semua sampah sudah dibersihkan dan peralatan kerja sudah diamankan</td>
            <td style="border: 1px solid black; text-align: center;">Ya &nbsp;&nbsp; N/A</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">Machine Guarding</td>
            <td colspan="2" style="border: 1px solid black; padding: 5px;">Semua <i>machine guarding</i> sudah dipasang kembali</td>
            <td style="border: 1px solid black; text-align: center;">Ya &nbsp;&nbsp; N/A</td>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 5px;">Tanggal:</th>
            <th style="border: 1px solid black; padding: 5px;">Jam:</th>
            <th colspan="2" style="border: 1px solid black; text-align: center; padding: 5px;">Tanda Tangan</th>
        </tr>
        <tr>
            <td style="border: 1px solid black; height: 40px;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black; text-align: center;"><i>Permit Requestor</i></td>
            <td style="border: 1px solid black; text-align: center;"><i>Permit Issuer</i></td>
        </tr>
    </tbody>
</table>

</body>
</html>