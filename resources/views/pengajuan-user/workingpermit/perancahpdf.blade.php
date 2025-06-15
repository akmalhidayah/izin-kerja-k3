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
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                1. Detail Pekerjaan Pemasangan/Pendirian Perancah
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; padding: 5px; width: 25%;">Lokasi pekerjaan:</td>
            <td style="border: 1px solid black; padding: 5px; width: 40%;"></td>
            <td style="border: 1px solid black; font-weight: bold; padding: 5px; width: 15%;">Tanggal:</td>
            <td style="border: 1px solid black; padding: 5px;"></td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; padding: 5px;">Uraian pekerjaan:</td>
            <td style="border: 1px solid black;" colspan="3" rowspan="2"></td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; padding: 5px;">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-weight: bold; padding: 5px;">
                Peralatan/perlengkapan yang akan digunakan pada pekerjaan:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; height: 50px;"></td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid black; font-weight: bold; padding: 5px;">
                Perkiraan jumlah pekerja yang akan terlibat dalam pekerjaan ini:
            </td>
            <td colspan="2" style="border: 1px solid black; font-weight: bold; padding: 5px;">
                Nomor gawat darurat yang harus dihubungi saat darurat:
            </td>
        </tr>
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                2. Persyaratan Kerja Aman untuk Pemasangan/Pendirian Perancah
            </th>
            <th style="background-color: black; color: white; text-align: center; width: 10%;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $items = [
                'Area kerja sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi, area dipasang safety line/barikade.' => 'Ya',
                '<i>Job Safety Analysis/Safe Working Procedure</i> sudah tersedia untuk jenis perancah yang akan dipasang/didirikan.' => 'Ya',
                'Scaffolder memiliki sertifikasi sebagai Teknisi Perancah, memakai FBH double hook sesuai dan paham penggunaannya.' => 'Ya',
                'Scaffolder yang memasang perancah gantung dinyatakan fit untuk bekerja di ketinggian, perancah gantung memerlukan Izin Kerja Bekerja di Ketinggian.' => 'Ya / N/A',
                'Material perancah dan semua aksesorisnya dalam kondisi layak pakai.' => 'Ya',
                'Perancah kompleks (misalnya perancah di permukaan yang tingginya sampai puluhan <i>lift</i>) telah di review dan disetujui oleh <i>Civil Engineer</i> untuk dipasang/didirikan.' => 'Ya / N/A',
            ];
        @endphp

        @foreach ($items as $text => $check)
        <tr>
            <td style="width: 5%; border: 1px solid black; text-align: center;">•</td>
            <td style="border: 1px solid black; padding: 5px;">{!! $text !!}</td>
            <td style="border: 1px solid black; text-align: center;">{!! $check !!}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                3. Permohonan Izin Kerja untuk Pemasangan/Pendirian Perancah
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">
                Permit Requestor:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px; text-align: justify;">
                Saya menyatakan bahwa semua persyaratan kerja aman untuk pemasangan/pendirian perancah yang telah ditentukan telah dipenuhi
                untuk dapat memulai memasang/mendirikan perancah ini.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Nama:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Tanda tangan:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Tanggal:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; height: 40px;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
        </tr>
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                4. Verifikasi Izin Kerja untuk Pemasangan/Pendirian Perancah
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">
                Scaffolding Permit Verificator:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px; text-align: justify;">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan memverifikasi semua persyaratan kerja aman untuk pemasangan/pendirian perancah yang telah ditentukan telah dipenuhi untuk dapat memulai memasang/mendirikan perancah ini.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Nama:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Tanda tangan:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Tanggal:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; height: 40px;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
        </tr>
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                5. Penerbitan Izin Kerja untuk Pemasangan/Pendirian Perancah
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">
                Permit Issuer:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px; text-align: justify;">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman untuk pemasangan/pendirian perancah yang telah ditentukan telah dipenuhi untuk dapat memulai memasang/mendirikan perancah ini.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Nama:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Tanda tangan:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Tanggal:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; height: 40px;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px;">
                Izin kerja ini berlaku dari tanggal: &nbsp; / &nbsp;/ &nbsp; jam: &nbsp; _______ &nbsp;
                sampai tanggal &nbsp; / &nbsp;/ &nbsp; jam: &nbsp; _______
            </td>
        </tr>
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                6. Pengesahan Izin Kerja untuk Pemasangan/Pendirian Perancah
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">
                Permit Authorizer:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px; text-align: justify;">
                Saya menyatakan bahwa saya telah memeriksa area kerja dan semua persyaratan kerja aman untuk pemasangan/pendirian perancah yang telah ditentukan telah dipenuhi untuk dapat memulai memasang/mendirikan perancah ini serta saya sudah menekankan apa saja <i>major hazards</i> dan pengendaliannya yang harus disosialisasikan oleh <i>Permit Receiver</i> kepada seluruh pekerja terkait.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Nama:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Tanda tangan:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Tanggal:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; height: 40px;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
        </tr>
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                7. Pelaksanaan Pekerjaan untuk Pemasangan/Pendirian Perancah
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 5px;">
                Permit Receiver:
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px; text-align: justify;">
                Saya menyatakan bahwa semua persyaratan kerja aman untuk pemasangan/pendirian perancah yang telah ditentukan telah dipenuhi untuk dapat memulai memasang/mendirikan perancah ini serta saya sudah mensosialisasikan apa saja <i>major hazards</i> dan pengendaliannya dari pekerjaan ini kepada seluruh pekerja terkait.
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Nama:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Tanda tangan:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Tanggal:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; width: 25%;">Jam:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; height: 40px;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black;"></td>
        </tr>
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                8. Persyaratan Keselamatan Perancah yang Dipasang/Didirikan
            </th>
            <th style="background-color: black; color: white; text-align: center; width: 12%;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $items = [
                'Semua kaki-kaki perancah (<i>vertical standard</i>) dipasang tegak lurus, terpasang <i>base plate</i> di dasarnya dan menyentuh permukaan.',
                'Semua ujung kaki-kaki perancah selain dipasang <i>base plate</i> juga dipasang <i>base pad</i> untuk meredam getaran/mencegah amblas.',
                'Pemasangan <i>transom</i> dan <i>ledger</i> berada di dalam <i>vertical standard</i>, pemasangan <i>transom/putlog</i> di atas <i>ledger</i>.',
                'Posisi baut <i>clamp</i> pengikat menghadap ke atas pada <i>ledger</i> yang diikatkan pada <i>vertical standard</i>.',
                'Jarak antara <i>vertical standard</i> secara menyamping/memanjang (<i>bay</i>) dan jarak antar <i>ledger</i> (<i>lift</i>) sesuai dengan kelasnya <i>light duty</i>/ <i>medium duty</i>/<i>heavy duty</i>.',
                'Semua <i>bracing</i> yang diperlukan sudah terpasang.',
                'Dipasang <i>outrigger</i> untuk menstabilkan perancah atau mengikatkan/menghubungkannya dengan aman pada struktur yang kuat.',
                '<i>Metal/wooden platform</i> yang dipasang kondisinya baik dan kuat menahan beban pekerja/material/peralatan, terikat kuat oleh kawat pengikat, dipasang sesuai tidak menimbulkan risiko tersandung, tidak ada celah terbuka yang bisa menimbulkan risiko terjatuh.',
                'Pagar pengaman dan toe <i>board</i> terpasang pada setiap lantai perancah.',
                'Tersedia tangga naik/turun dengan penempatan dan kemiringan yang aman, tangga melebihi 6 meter dipasang secara <i>zig-zag</i>, terikat kuat pada perancah.',
                'Batas demarkasi/<i>safety line</i> telah dipasang di sekeliling bawah struktur perancah.',
                'Kabel listrik dekat struktur perancah telah diamankan untuk mencegah risiko perancah terkena aliran listrik.',
                'Dipasang katrol yang aman untuk menaikkan/menurunkan barang.'
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
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; text-align: left; padding: 5px;">
                9. Rekomendasi Persyaratan Keselamatan Perancah Tambahan dari <i>Permit Verificator/Permit Issuer</i> <span style="font-weight: normal;">(jika ada)</span>
            </th>
            <th style="background-color: black; color: white; text-align: center; width: 8%;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2" style="border: 1px solid black; height: 60px;"></td>
            <td style="border: 1px solid black; text-align: center;">Ya</td>
        </tr>
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="4" style="background-color: black; color: white; font-weight: bold; text-align: left; padding: 5px;">
                10. Persetujuan Perancah Layak Digunakan
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" style="border: 1px solid black; padding: 5px; text-align: justify;">
                <i>Scaffolding Permit Verificator, Permit Issuer & Permit Authorizer</i><br>
                Saya menyatakan bahwa saya telah memeriksa perancah dan semua persyaratan keselamatan perancah yang telah ditentukan dan atau rekomendasi persyaratan keselamatan perancah tambahan dari <i>Permit Verificator/Permit Issuer</i> telah dipenuhi untuk perancah ini dapat digunakan.
            </td>
        </tr>
        <tr style="text-align: center; font-style: italic; font-weight: bold;">
            <td style="border: 1px solid black;">Scaffolding Permit Verificator</td>
            <td style="border: 1px solid black;">Permit Issuer</td>
            <td colspan="2" style="border: 1px solid black;">Permit Authorizer</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; height: 40px;"></td>
            <td style="border: 1px solid black;"></td>
            <td colspan="2" style="border: 1px solid black;"></td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid black; font-weight: bold;">Perancah ini berlaku dari tanggal: &nbsp;&nbsp;&nbsp;/ &nbsp;&nbsp;&nbsp;/ &nbsp;&nbsp;&nbsp; jam:</td>
            <td colspan="2" style="border: 1px solid black; font-weight: bold;">sampai tanggal &nbsp;&nbsp;&nbsp;/ &nbsp;&nbsp;&nbsp;/ &nbsp;&nbsp;&nbsp; jam:</td>
        </tr>
    </tbody>
</table>
<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <thead>
        <tr>
            <th colspan="2" style="background-color: black; color: white; text-align: left; padding: 5px;">
                11. Penutupan Izin Kerja
            </th>
            <th style="background-color: black; color: white; text-align: center; width: 8%;">(lingkari)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 4px;">Lock & Tag</td>
            <td style="border: 1px solid black; padding: 4px;">Semua <i>lock & tag</i> sudah dilepas</td>
            <td style="border: 1px solid black; text-align: center;">Ya N/A</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 4px;">Sampah & Peralatan Kerja</td>
            <td style="border: 1px solid black; padding: 4px;">Semua sampah sudah dibersihkan dan peralatan kerja sudah diamankan</td>
            <td style="border: 1px solid black; text-align: center;">Ya</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-style: italic; font-weight: bold; padding: 4px;">Machine Guarding</td>
            <td style="border: 1px solid black; padding: 4px;">Semua <i>machine guarding</i> sudah dipasang kembali</td>
            <td style="border: 1px solid black; text-align: center;">Ya N/A</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; font-weight: bold; text-align: center; padding: 6px;">Tanggal:</td>
            <td style="border: 1px solid black; font-weight: bold; text-align: center;">Jam:</td>
            <td style="border: 1px solid black;" colspan="2">
                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                    <tr>
                        <td style="text-align: center; font-weight: bold;">Tanda Tangan</td>
                    </tr>
                    <tr>
                        <td style="display: flex; justify-content: space-between; padding: 0 20px;">
                            <span style="font-style: italic;">Permit Requestor</span>
                            <span style="font-style: italic;">Permit Issuer</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
</table>

</body>
</html>